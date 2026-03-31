<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MeatInventory extends Model
{
    protected $table = 'meat_inventory';

    protected $fillable = ['animal_id', 'weight_kg', 'sold_weight_kg', 'status', 'delivered_at', 'notes'];

    protected $casts = [
        'weight_kg'      => 'float',
        'sold_weight_kg' => 'float',
        'delivered_at'   => 'datetime',
    ];

    public function animal(): BelongsTo
    {
        return $this->belongsTo(Animal::class);
    }

    public function sales(): HasMany
    {
        return $this->hasMany(MeatSale::class);
    }

    public function remainingWeight(): float
    {
        return max(0, $this->weight_kg - $this->sold_weight_kg);
    }

    public function isSoldOut(): bool
    {
        return $this->remainingWeight() <= 0;
    }
}
