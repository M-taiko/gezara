<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Advance extends Model
{
    protected $fillable = ['advance_number', 'type', 'customer_id', 'supplier_id', 'wallet_id', 'amount', 'remaining', 'notes', 'status', 'date'];
    protected $casts = ['amount' => 'float', 'remaining' => 'float', 'date' => 'datetime'];

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            if (!$model->advance_number) {
                $prefix = $model->type === 'customer' ? 'ADV-C' : 'ADV-S';
                $year = now()->year;
                $count = static::where('type', $model->type)->whereYear('created_at', $year)->count() + 1;
                $model->advance_number = "{$prefix}-{$year}-" . str_pad($count, 4, '0', STR_PAD_LEFT);
            }
        });
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class);
    }

    public function wallet(): BelongsTo
    {
        return $this->belongsTo(Wallet::class);
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(AdvanceTransaction::class);
    }

    public function getReceivedAmount(): float
    {
        return $this->transactions()->where('type', 'receipt')->sum('amount');
    }

    public function getReturnedAmount(): float
    {
        return $this->transactions()->where('type', 'return')->sum('amount');
    }

    public function getName(): string
    {
        if ($this->type === 'customer') {
            return $this->customer?->name ?? '—';
        }
        return $this->supplier?->name ?? '—';
    }

    public function getTypeLabel(): string
    {
        return $this->type === 'customer' ? 'سلف عميل' : 'سلف مورد';
    }

    public function getStatusLabel(): string
    {
        return [
            'active' => 'نشط',
            'settled' => 'مغلقة',
            'cancelled' => 'ملغاة',
        ][$this->status] ?? $this->status;
    }
}
