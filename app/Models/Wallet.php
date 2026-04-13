<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Wallet extends Model
{
    protected $fillable = ['name', 'type', 'balance', 'notes', 'is_active'];

    protected $casts = [
        'balance' => 'float',
        'is_active' => 'boolean',
    ];

    public function transactions(): HasMany
    {
        return $this->hasMany(WalletTransaction::class);
    }

    public function getTypeLabel(): string
    {
        return [
            'cash' => '💵 نقدي',
            'mobile' => '📲 محفظة رقمية',
            'bank' => '🏦 بنك',
        ][$this->type] ?? $this->type;
    }
}
