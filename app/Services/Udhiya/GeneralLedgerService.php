<?php

namespace App\Services\Udhiya;

use App\Models\Contract;
use App\Models\Payment;
use App\Models\Advance;
use App\Models\AdvanceTransaction;
use App\Models\Purchase;
use App\Models\MeatSale;
use Illuminate\Support\Collection;

class GeneralLedgerService
{
    public function getGeneralLedger($filters = [])
    {
        $transactionType = $filters['transaction_type'] ?? null;
        $startDate = $filters['start_date'] ?? null;
        $endDate = $filters['end_date'] ?? null;
        $walletId = $filters['wallet_id'] ?? null;

        $transactions = collect();

        // 1. Contracts (صكوك) - Debit entry (حق على العميل)
        if (!$transactionType || $transactionType === 'contract') {
            $contracts = $this->getContractTransactions($startDate, $endDate);
            $transactions = $transactions->concat($contracts);
        }

        // 2. Payments (الدفعات) - Credit entry (استلام من العميل)
        if (!$transactionType || $transactionType === 'payment') {
            $payments = $this->getPaymentTransactions($startDate, $endDate, $walletId);
            $transactions = $transactions->concat($payments);
        }

        // 3. Advances (السلف) - Receipts are debit, returns are credit
        if (!$transactionType || $transactionType === 'advance') {
            $advances = $this->getAdvanceTransactions($startDate, $endDate, $walletId);
            $transactions = $transactions->concat($advances);
        }

        // 4. Purchases (المشتريات) - Debit entry (التزام على الشركة)
        if (!$transactionType || $transactionType === 'purchase') {
            $purchases = $this->getPurchaseTransactions($startDate, $endDate);
            $transactions = $transactions->concat($purchases);
        }

        // 5. Sales (المبيعات) - Credit entry (استلام نقدي من المبيعات)
        if (!$transactionType || $transactionType === 'sale') {
            $sales = $this->getSaleTransactions($startDate, $endDate);
            $transactions = $transactions->concat($sales);
        }

        return $transactions->sortByDesc('date')->values();
    }

    private function getContractTransactions($startDate, $endDate)
    {
        return Contract::with('customer')
            ->when($startDate, fn($q) => $q->whereDate('created_at', '>=', $startDate))
            ->when($endDate, fn($q) => $q->whereDate('created_at', '<=', $endDate))
            ->get()
            ->map(function ($contract) {
                return [
                    'id' => $contract->id,
                    'type' => 'contract',
                    'transaction_type' => 'صك عميل',
                    'date' => $contract->created_at,
                    'reference' => $contract->contract_number,
                    'reference_url' => route('udhiya.contracts.show', $contract),
                    'description' => $contract->customer?->name ?? '—',
                    'debit' => $contract->total_amount,
                    'credit' => 0,
                    'wallet_name' => '—',
                    'is_contract' => true,
                    'collected' => $contract->paid_amount,
                    'remaining' => $contract->remaining_amount,
                    'total' => $contract->total_amount,
                    'status' => $contract->status,
                ];
            });
    }

    private function getPaymentTransactions($startDate, $endDate, $walletId)
    {
        return Payment::with('contract.customer', 'wallet')
            ->when($walletId, fn($q) => $q->where('wallet_id', $walletId))
            ->when($startDate, fn($q) => $q->whereDate('date', '>=', $startDate))
            ->when($endDate, fn($q) => $q->whereDate('date', '<=', $endDate))
            ->get()
            ->map(function ($payment) {
                return [
                    'id' => $payment->id,
                    'type' => 'payment',
                    'transaction_type' => 'دفع من عميل',
                    'date' => $payment->date,
                    'reference' => $payment->receipt_number,
                    'reference_url' => $payment->contract ? route('udhiya.contracts.show', $payment->contract) : null,
                    'description' => 'صك: ' . ($payment->contract?->contract_number ?? '—') . ' — ' . ($payment->contract?->customer?->name ?? '—'),
                    'debit' => 0,
                    'credit' => $payment->amount,
                    'wallet_name' => $payment->wallet?->name ?? '—',
                    'is_contract' => false,
                    'status' => 'completed',
                ];
            });
    }

    private function getAdvanceTransactions($startDate, $endDate, $walletId)
    {
        return AdvanceTransaction::with('advance.customer', 'advance.supplier', 'wallet')
            ->when($walletId, fn($q) => $q->where('wallet_id', $walletId))
            ->when($startDate, fn($q) => $q->whereDate('date', '>=', $startDate))
            ->when($endDate, fn($q) => $q->whereDate('date', '<=', $endDate))
            ->get()
            ->map(function ($transaction) {
                return [
                    'id' => $transaction->id,
                    'type' => 'advance',
                    'transaction_type' => $transaction->type === 'receipt' ? 'استلام سلف' : 'رد سلف',
                    'date' => $transaction->date,
                    'reference' => $transaction->advance->advance_number,
                    'reference_url' => route('udhiya.advances.show', $transaction->advance),
                    'description' => ($transaction->advance->type === 'customer' ? 'عميل' : 'مورد') . ': ' . $transaction->advance->getName(),
                    'debit' => $transaction->type === 'receipt' ? $transaction->amount : 0,
                    'credit' => $transaction->type === 'return' ? $transaction->amount : 0,
                    'wallet_name' => $transaction->wallet?->name ?? '—',
                    'is_contract' => false,
                    'status' => 'completed',
                ];
            });
    }

    private function getPurchaseTransactions($startDate, $endDate)
    {
        return Purchase::with('supplier')
            ->when($startDate, fn($q) => $q->whereDate('date', '>=', $startDate))
            ->when($endDate, fn($q) => $q->whereDate('date', '<=', $endDate))
            ->get()
            ->map(function ($purchase) {
                return [
                    'id' => $purchase->id,
                    'type' => 'purchase',
                    'transaction_type' => 'شراء من مورد',
                    'date' => $purchase->date,
                    'reference' => 'PUR-' . str_pad($purchase->id, 4, '0', STR_PAD_LEFT),
                    'reference_url' => route('udhiya.purchases.show', $purchase),
                    'description' => $purchase->supplier?->name ?? '—',
                    'debit' => $purchase->total,
                    'credit' => 0,
                    'wallet_name' => '—',
                    'is_contract' => false,
                    'paid' => $purchase->paid,
                    'remaining' => $purchase->total - $purchase->paid,
                    'status' => $purchase->status ?? 'pending',
                ];
            });
    }

    private function getSaleTransactions($startDate, $endDate)
    {
        return MeatSale::with('inventory')
            ->when($startDate, fn($q) => $q->whereDate('sale_date', '>=', $startDate))
            ->when($endDate, fn($q) => $q->whereDate('sale_date', '<=', $endDate))
            ->get()
            ->map(function ($sale) {
                return [
                    'id' => $sale->id,
                    'type' => 'sale',
                    'transaction_type' => 'بيع لحوم',
                    'date' => $sale->sale_date,
                    'reference' => 'SALE-' . str_pad($sale->id, 4, '0', STR_PAD_LEFT),
                    'reference_url' => null,
                    'description' => $sale->customer_name ?? '—',
                    'debit' => 0,
                    'credit' => $sale->total_amount,
                    'wallet_name' => '—',
                    'is_contract' => false,
                    'status' => 'completed',
                ];
            });
    }

    public function calculateTotals(Collection $transactions)
    {
        return [
            'total_debit' => $transactions->sum('debit'),
            'total_credit' => $transactions->sum('credit'),
            'net_balance' => $transactions->sum('debit') - $transactions->sum('credit'),
            'contracts_receivable' => $transactions->where('type', 'contract')->sum('debit'),
            'payments_received' => $transactions->where('type', 'payment')->sum('credit'),
            'advances_out' => $transactions->where('type', 'advance')->sum('debit'),
            'advances_returned' => $transactions->where('type', 'advance')->sum('credit'),
            'purchases_payable' => $transactions->where('type', 'purchase')->sum('debit'),
            'sales_revenue' => $transactions->where('type', 'sale')->sum('credit'),
        ];
    }

    public function getSourceTotals($filters = [])
    {
        $startDate = $filters['start_date'] ?? null;
        $endDate = $filters['end_date'] ?? null;

        // Get totals directly from source tables
        $contracts = Contract::when($startDate, fn($q) => $q->whereDate('created_at', '>=', $startDate))
            ->when($endDate, fn($q) => $q->whereDate('created_at', '<=', $endDate));

        $payments = Payment::when($startDate, fn($q) => $q->whereDate('date', '>=', $startDate))
            ->when($endDate, fn($q) => $q->whereDate('date', '<=', $endDate));

        $purchases = Purchase::when($startDate, fn($q) => $q->whereDate('date', '>=', $startDate))
            ->when($endDate, fn($q) => $q->whereDate('date', '<=', $endDate));

        $sales = MeatSale::when($startDate, fn($q) => $q->whereDate('sale_date', '>=', $startDate))
            ->when($endDate, fn($q) => $q->whereDate('sale_date', '<=', $endDate));

        $contractsTotal = (clone $contracts)->sum('total_amount');
        $contractsPaid = (clone $contracts)->sum('paid_amount');
        $contractsRemaining = (clone $contracts)->sum('remaining_amount');
        $paymentsTotal = (clone $payments)->sum('amount');
        $purchasesTotal = (clone $purchases)->sum('total');
        $salesTotal = (clone $sales)->sum('total_amount');

        return [
            'total_debit' => $contractsTotal + $purchasesTotal,
            'total_credit' => $paymentsTotal + $salesTotal,
            'net_balance' => ($contractsTotal + $purchasesTotal) - ($paymentsTotal + $salesTotal),
            'contracts_receivable' => $contractsTotal,
            'contracts_paid' => $contractsPaid,
            'contracts_remaining' => $contractsRemaining,
            'payments_received' => $paymentsTotal,
            'advances_out' => 0,
            'advances_returned' => 0,
            'purchases_payable' => $purchasesTotal,
            'sales_revenue' => $salesTotal,
        ];
    }
}
