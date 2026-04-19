<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AdvanceTransaction extends Model
{
    protected $fillable = ['advance_id', 'type', 'wallet_id', 'amount', 'notes', 'date'];
    protected $casts = ['amount' => 'float', 'date' => 'datetime'];

    public function advance(): BelongsTo
    {
        return $this->belongsTo(Advance::class);
    }

    public function wallet(): BelongsTo
    {
        return $this->belongsTo(Wallet::class);
    }

    public function getTypeLabel(): string
    {
        return $this->type === 'receipt' ? 'استلام' : 'رد';
    }
}
