<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ContractRequest extends Model
{
    protected $fillable = [
        'animal_id', 'customer_name', 'customer_phone', 'customer_email',
        'share_type', 'share_price', 'notes', 'status'
    ];

    protected $casts = [
        'share_price' => 'float',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function animal(): BelongsTo
    {
        return $this->belongsTo(Animal::class);
    }

    public function statusLabel(): string
    {
        return match($this->status) {
            'pending' => '⏳ معلقة',
            'approved' => '✅ موافق عليها',
            'rejected' => '❌ مرفوضة',
            'converted' => '📋 تحويل لصك',
            default => $this->status ?? 'غير محدد',
        };
    }
}
