<?php

namespace App\Services\Udhiya;

use App\Models\Contract;
use App\Models\Payment;
use App\Models\Treasury;
use App\Models\Wallet;
use Illuminate\Support\Facades\DB;

class PaymentService
{
    public function __construct(private AccountingService $accounting, private WalletService $walletService) {}

    public function store(Contract $contract, array $data): Payment
    {
        \Illuminate\Support\Facades\Log::debug('PaymentService::store called', [
            'contract_id' => $contract->id,
            'amount' => $data['amount'] ?? null,
            'payment_method' => $data['payment_method'] ?? null,
        ]);

        return DB::transaction(function () use ($contract, $data) {
            if (round($data['amount'], 2) > round($contract->remaining_amount, 2)) {
                throw new \RuntimeException(
                    "المبلغ ({$data['amount']}) أكبر من المتبقي ({$contract->remaining_amount})."
                );
            }

            // Ensure payment_method is valid string
            $method = $data['payment_method'] ?? 'cash';
            if (empty($method) || !is_string($method)) {
                $method = 'cash';
            }

            $paymentData = [
                'contract_id'      => $contract->id,
                'amount'           => (float) $data['amount'],
                'payment_method'   => $method,
                'receipt_number'   => $data['receipt_number'] ?? null,
                'reference_number' => $data['reference_number'] ?? null,
                'date'             => $data['date'],
                'notes'            => $data['notes'] ?? null,
                'wallet_id'        => !empty($data['wallet_id']) ? (int) $data['wallet_id'] : null,
            ];

            // Handle attachment paths if provided
            if (!empty($data['attachment_paths'])) {
                $paymentData['attachment_paths'] = json_encode($data['attachment_paths']);
                $paymentData['attachments'] = collect($data['attachment_paths'])
                    ->map(fn($p) => basename($p))
                    ->toArray();
            }

            $payment = Payment::create($paymentData);

            // Update contract financials
            $newPaid      = $contract->paid_amount + $data['amount'];
            $newRemaining = $contract->total_amount - $newPaid;
            $newStatus    = $newRemaining <= 0 ? 'completed' : $contract->status;

            $contract->update([
                'paid_amount'      => $newPaid,
                'remaining_amount' => $newRemaining,
                'status'           => $newStatus,
            ]);

            // Register payment in wallet if provided
            if ($data['wallet_id'] ?? null) {
                $wallet = Wallet::findOrFail($data['wallet_id']);
                $this->walletService->credit(
                    $wallet,
                    $data['amount'],
                    $data['date'],
                    Payment::class,
                    $payment->id,
                    'دفعة من ' . $contract->customer->name . ' — إيصال #' . $payment->receipt_number
                );
            } else {
                // Legacy: keep old treasury entry if no wallet selected
                Treasury::create([
                    'type'           => 'in',
                    'amount'         => $data['amount'],
                    'reference_type' => Payment::class,
                    'reference_id'   => $payment->id,
                    'description'    => 'دفعة من ' . $contract->customer->name . ' — إيصال #' . $payment->receipt_number,
                    'date'           => $data['date'],
                ]);
            }

            $this->accounting->recordCustomerPayment($payment);

            return $payment;
        });
    }

    public function delete(Payment $payment): void
    {
        DB::transaction(function () use ($payment) {
            $contract = $payment->contract;

            // Reverse contract financials
            $newPaid      = $contract->paid_amount - $payment->amount;
            $newRemaining = $contract->total_amount - $newPaid;
            $newStatus    = 'active'; // Revert to active since it's no longer fully paid

            $contract->update([
                'paid_amount'      => $newPaid,
                'remaining_amount' => $newRemaining,
                'status'           => $newStatus,
            ]);

            // Reverse wallet transaction if wallet was used
            if ($payment->wallet_id) {
                $wallet = Wallet::findOrFail($payment->wallet_id);
                $this->walletService->debit(
                    $wallet,
                    $payment->amount,
                    $payment->date,
                    Payment::class,
                    $payment->id,
                    'استرجاع دفعة من ' . $contract->customer->name . ' — إيصال #' . $payment->receipt_number
                );
            } else {
                // Legacy: remove old treasury entry if it exists
                Treasury::where('reference_type', Payment::class)
                    ->where('reference_id', $payment->id)
                    ->delete();
            }

            // Reverse accounting entry
            $this->accounting->reverseCustomerPayment($payment);

            // Finally delete the payment
            $payment->delete();
        });
    }

    public function update(Payment $payment, array $data): Payment
    {
        return DB::transaction(function () use ($payment, $data) {
            $contract = $payment->contract;
            $oldAmount = $payment->amount;
            $newAmount = $data['amount'];

            // Check if updating to a different contract
            $newContract = null;
            if (isset($data['contract_id']) && $data['contract_id'] != $contract->id) {
                $newContract = Contract::findOrFail($data['contract_id']);
            } else {
                $newContract = $contract;
            }

            // Validate new amount doesn't exceed target contract's remaining
            // If switching contracts, check new contract's remaining + old payment amount (since we're removing from old)
            $maxAmount = $newContract->remaining_amount + ($newContract->id == $contract->id ? $oldAmount : 0);
            if ($newAmount > $maxAmount) {
                throw new \RuntimeException("المبلغ يتجاوز الحد المسموح ({$maxAmount}).");
            }

            // If amount changed or contract changed, reverse old and record new
            $contractChanged = $newContract->id != $contract->id;
            if ($newAmount != $oldAmount || $contractChanged) {
                // Reverse old accounting entry
                $this->accounting->reverseCustomerPayment($payment);

                // Reverse old wallet transaction if any
                if ($payment->wallet_id) {
                    $wallet = Wallet::findOrFail($payment->wallet_id);
                    $this->walletService->debit(
                        $wallet,
                        $oldAmount,
                        $payment->date,
                        Payment::class,
                        $payment->id,
                        'تعديل دفعة من ' . $contract->customer->name . ' — إيصال #' . $payment->receipt_number
                    );
                } else {
                    Treasury::where('reference_type', Payment::class)
                        ->where('reference_id', $payment->id)
                        ->delete();
                }

                // Update old contract financials (remove old amount)
                $contract->decrement('paid_amount', $oldAmount);
                $contract->increment('remaining_amount', $oldAmount);

                // Update payment with new amount, contract, and details
                $method = $data['payment_method'] ?? $payment->payment_method;
                if (empty($method) || !is_string($method)) {
                    $method = $payment->payment_method ?? 'cash';
                }

                $updateData = [
                    'contract_id'     => $newContract->id,
                    'amount'          => $newAmount,
                    'payment_method'  => $method,
                    'date'            => $data['date'] ?? $payment->date,
                    'notes'           => $data['notes'] ?? null,
                    'wallet_id'       => $data['wallet_id'] ?? null,
                    'receipt_number'  => $data['receipt_number'] ?? $payment->receipt_number,
                    'reference_number' => $data['reference_number'] ?? $payment->reference_number,
                ];

                // Handle attachment updates
                if (!empty($data['attachment_paths'])) {
                    $updateData['attachment_paths'] = json_encode($data['attachment_paths']);
                    $updateData['attachments'] = collect($data['attachment_paths'])
                        ->map(fn($p) => basename($p))
                        ->toArray();
                }

                $payment->update($updateData);

                // Record new accounting entry
                $this->accounting->recordCustomerPayment($payment);

                // Register new wallet transaction if provided
                if ($data['wallet_id'] ?? null) {
                    $wallet = Wallet::findOrFail($data['wallet_id']);
                    $this->walletService->credit(
                        $wallet,
                        $newAmount,
                        $payment->date,
                        Payment::class,
                        $payment->id,
                        'دفعة من ' . $newContract->customer->name . ' — إيصال #' . $payment->receipt_number
                    );
                } else {
                    Treasury::create([
                        'type'           => 'in',
                        'amount'         => $newAmount,
                        'reference_type' => Payment::class,
                        'reference_id'   => $payment->id,
                        'description'    => 'دفعة من ' . $newContract->customer->name . ' — إيصال #' . $payment->receipt_number,
                        'date'           => $payment->date,
                    ]);
                }

                // Update new contract with new amount
                $newPaid      = $newContract->paid_amount + $newAmount;
                $newRemaining = $newContract->total_amount - $newPaid;
                $newStatus    = $newRemaining <= 0 ? 'completed' : 'active';

                $newContract->update([
                    'paid_amount'      => $newPaid,
                    'remaining_amount' => $newRemaining,
                    'status'           => $newStatus,
                ]);
            } else {
                // Just update non-amount, non-contract fields
                $method = $data['payment_method'] ?? $payment->payment_method;
                if (empty($method) || !is_string($method)) {
                    $method = $payment->payment_method ?? 'cash';
                }

                $updateData = [
                    'payment_method'  => $method,
                    'date'            => $data['date'] ?? $payment->date,
                    'notes'           => $data['notes'] ?? null,
                    'wallet_id'       => $data['wallet_id'] ?? null,
                    'receipt_number'  => $data['receipt_number'] ?? $payment->receipt_number,
                    'reference_number' => $data['reference_number'] ?? $payment->reference_number,
                ];

                // Handle attachment updates
                if (!empty($data['attachment_paths'])) {
                    $updateData['attachment_paths'] = json_encode($data['attachment_paths']);
                    $updateData['attachments'] = collect($data['attachment_paths'])
                        ->map(fn($p) => basename($p))
                        ->toArray();
                }

                $payment->update($updateData);
            }

            return $payment->fresh();
        });
    }
}
