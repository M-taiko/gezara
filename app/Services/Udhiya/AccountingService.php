<?php

namespace App\Services\Udhiya;

use App\Models\Account;
use App\Models\JournalEntry;
use App\Models\JournalEntryItem;
use App\Models\Purchase;
use App\Models\Contract;
use App\Models\Payment;
use Illuminate\Support\Facades\DB;

class AccountingService
{
    // Account codes
    const CASH       = '1000';
    const INVENTORY  = '1100';
    const RECEIVABLE = '3000';
    const PAYABLE    = '2000';
    const REVENUE    = '4000';
    const COGS       = '5000';

    public function recordPurchase(Purchase $purchase): JournalEntry
    {
        $items = [
            ['code' => self::INVENTORY, 'type' => 'debit',  'amount' => $purchase->total, 'desc' => 'مخزون حيوانات — فاتورة شراء #' . $purchase->id],
            ['code' => self::PAYABLE,   'type' => 'credit', 'amount' => $purchase->total, 'desc' => 'ذمة مورد — ' . $purchase->supplier->name],
        ];

        return $this->createEntry(
            $purchase,
            'شراء حيوانات من ' . $purchase->supplier->name,
            $purchase->date,
            $items
        );
    }

    public function recordContract(Contract $contract): JournalEntry
    {
        $items = [
            ['code' => self::RECEIVABLE, 'type' => 'debit',  'amount' => $contract->total_amount, 'desc' => 'ذمة عميل — ' . $contract->customer->name],
            ['code' => self::REVENUE,    'type' => 'credit', 'amount' => $contract->total_amount, 'desc' => 'إيراد مبيعات — صك #' . $contract->contract_number],
        ];

        return $this->createEntry(
            $contract,
            'صك بيع #' . $contract->contract_number . ' — ' . $contract->customer->name,
            $contract->created_at->toDateString(),
            $items
        );
    }

    public function recordCustomerPayment(Payment $payment): JournalEntry
    {
        $items = [
            ['code' => self::CASH,       'type' => 'debit',  'amount' => $payment->amount, 'desc' => 'تحصيل نقدي — إيصال #' . $payment->receipt_number],
            ['code' => self::RECEIVABLE, 'type' => 'credit', 'amount' => $payment->amount, 'desc' => 'ذمة عميل — ' . $payment->contract->customer->name],
        ];

        return $this->createEntry(
            $payment,
            'دفعة من ' . $payment->contract->customer->name . ' — إيصال #' . $payment->receipt_number,
            $payment->date,
            $items
        );
    }

    private function createEntry(mixed $model, string $description, string $date, array $lines): JournalEntry
    {
        $debits  = array_sum(array_column(array_filter($lines, fn($l) => $l['type'] === 'debit'),  'amount'));
        $credits = array_sum(array_column(array_filter($lines, fn($l) => $l['type'] === 'credit'), 'amount'));

        if (round($debits, 2) !== round($credits, 2)) {
            throw new \RuntimeException("القيد غير متوازن: مدين={$debits} دائن={$credits}");
        }

        $entry = JournalEntry::create([
            'reference_type' => get_class($model),
            'reference_id'   => $model->id,
            'description'    => $description,
            'date'           => $date,
        ]);

        foreach ($lines as $line) {
            $account = Account::findByCode($line['code']);
            JournalEntryItem::create([
                'journal_entry_id' => $entry->id,
                'account_id'       => $account->id,
                'type'             => $line['type'],
                'amount'           => $line['amount'],
                'description'      => $line['desc'] ?? null,
            ]);

            // Update account running balance
            if ($line['type'] === 'debit') {
                if (in_array($account->type, ['asset', 'expense'])) {
                    $account->increment('balance', $line['amount']);
                } else {
                    $account->decrement('balance', $line['amount']);
                }
            } else {
                if (in_array($account->type, ['liability', 'revenue', 'equity'])) {
                    $account->increment('balance', $line['amount']);
                } else {
                    $account->decrement('balance', $line['amount']);
                }
            }
        }

        return $entry;
    }
}
