<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Warehouse extends Model
{
    protected $fillable = ['name', 'description'];

    public function animals(): HasMany
    {
        return $this->hasMany(Animal::class);
    }
}
