<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\SupplierPayment;

class Purchase extends Model
{
    protected $fillable = ['supplier_id', 'date', 'notes', 'total', 'paid', 'status'];
    protected $casts = ['date' => 'date', 'total' => 'float', 'paid' => 'float'];

    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(PurchaseItem::class);
    }

    public function animals(): HasMany
    {
        return $this->hasMany(Animal::class);
    }

    public function supplierPayments(): HasMany
    {
        return $this->hasMany(SupplierPayment::class);
    }

    public function remaining(): float
    {
        return $this->total - $this->paid;
    }
}
