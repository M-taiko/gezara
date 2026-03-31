<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MeatSale extends Model
{
    protected $fillable = [
        'meat_inventory_id', 'customer_name', 'customer_phone',
        'weight_kg', 'price_per_kg', 'total_amount', 'sale_date', 'notes',
    ];

    protected $casts = [
        'weight_kg'    => 'float',
        'price_per_kg' => 'float',
        'total_amount' => 'float',
        'sale_date'    => 'date',
    ];

    public function inventory(): BelongsTo
    {
        return $this->belongsTo(MeatInventory::class, 'meat_inventory_id');
    }
}
