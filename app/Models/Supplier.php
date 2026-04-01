<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Supplier extends Model
{
    protected $fillable = ['name', 'phone', 'address', 'notes', 'balance'];
    protected $casts = ['balance' => 'float'];

    public function purchases(): HasMany
    {
        return $this->hasMany(Purchase::class);
    }

    public function animals(): HasMany
    {
        return $this->hasMany(Animal::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(SupplierPayment::class);
    }

    /** إجمالي قيمة المشتريات */
    public function totalPurchases(): float
    {
        return (float) $this->purchases()->sum('total');
    }
}
