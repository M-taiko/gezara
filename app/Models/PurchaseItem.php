<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PurchaseItem extends Model
{
    protected $fillable = [
        'purchase_id', 'product_id', 'quantity', 'weight', 'cost_per_unit', 'total',
        'price_full', 'price_half', 'price_third', 'price_quarter', 'price_five', 'price_six', 'price_seven'
    ];
    protected $casts = [
        'quantity' => 'integer',
        'weight' => 'float',
        'cost_per_unit' => 'float',
        'total' => 'float',
        'price_full' => 'float',
        'price_half' => 'float',
        'price_third' => 'float',
        'price_quarter' => 'float',
        'price_five' => 'float',
        'price_six' => 'float',
        'price_seven' => 'float',
    ];

    public function purchase(): BelongsTo
    {
        return $this->belongsTo(Purchase::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}
