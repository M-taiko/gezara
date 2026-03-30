<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Customer extends Model
{
    protected $fillable = ['name', 'phone', 'address', 'notes'];

    public function contracts(): HasMany
    {
        return $this->hasMany(Contract::class);
    }

    public function groupMembers(): HasMany
    {
        return $this->hasMany(SlaughterGroupMember::class);
    }

    public function totalContracts(): int { return $this->contracts()->count(); }
    public function totalPaid(): float    { return (float) $this->contracts()->sum('paid_amount'); }
    public function totalDue(): float     { return (float) $this->contracts()->sum('remaining_amount'); }
}
