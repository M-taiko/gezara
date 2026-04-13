<?php

namespace App\Services\Udhiya;

use App\Models\Wallet;
use App\Models\WalletTransaction;
use App\Models\WalletTransfer;
use Illuminate\Support\Facades\DB;

class WalletService
{
    public function transfer(Wallet $from, Wallet $to, float $amount, string $date, ?string $notes = null): WalletTransfer
    {
        return DB::transaction(function () use ($from, $to, $amount, $date, $notes) {
            // Debit source wallet
            $from->decrement('balance', $amount);
            WalletTransaction::create([
                'wallet_id' => $from->id,
                'type' => 'out',
                'amount' => $amount,
                'reference_type' => WalletTransfer::class,
                'description' => 'تحويل لـ ' . $to->name,
                'date' => $date,
            ]);

            // Credit destination wallet
            $to->increment('balance', $amount);
            WalletTransaction::create([
                'wallet_id' => $to->id,
                'type' => 'in',
                'amount' => $amount,
                'reference_type' => WalletTransfer::class,
                'description' => 'تحويل من ' . $from->name,
                'date' => $date,
            ]);

            // Create transfer record
            return WalletTransfer::create([
                'from_wallet_id' => $from->id,
                'to_wallet_id' => $to->id,
                'amount' => $amount,
                'date' => $date,
                'notes' => $notes,
            ]);
        });
    }

    public function credit(Wallet $wallet, float $amount, string $date, ?string $refType = null, ?int $refId = null, ?string $desc = null): void
    {
        DB::transaction(function () use ($wallet, $amount, $date, $refType, $refId, $desc) {
            $wallet->increment('balance', $amount);
            WalletTransaction::create([
                'wallet_id' => $wallet->id,
                'type' => 'in',
                'amount' => $amount,
                'reference_type' => $refType,
                'reference_id' => $refId,
                'description' => $desc,
                'date' => $date,
            ]);
        });
    }

    public function debit(Wallet $wallet, float $amount, string $date, ?string $refType = null, ?int $refId = null, ?string $desc = null): void
    {
        DB::transaction(function () use ($wallet, $amount, $date, $refType, $refId, $desc) {
            $wallet->decrement('balance', $amount);
            WalletTransaction::create([
                'wallet_id' => $wallet->id,
                'type' => 'out',
                'amount' => $amount,
                'reference_type' => $refType,
                'reference_id' => $refId,
                'description' => $desc,
                'date' => $date,
            ]);
        });
    }
}
