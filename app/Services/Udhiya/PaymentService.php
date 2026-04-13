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
        return DB::transaction(function () use ($contract, $data) {
            if (round($data['amount'], 2) > round($contract->remaining_amount, 2)) {
                throw new \RuntimeException(
                    "المبلغ ({$data['amount']}) أكبر من المتبقي ({$contract->remaining_amount})."
                );
            }

            $payment = Payment::create([
                'contract_id'    => $contract->id,
                'amount'         => $data['amount'],
                'payment_method' => $data['payment_method'] ?? 'cash',
                'date'           => $data['date'],
                'notes'          => $data['notes'] ?? null,
                'wallet_id'      => $data['wallet_id'] ?? null,
            ]);

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
}
