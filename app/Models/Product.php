<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Product extends Model
{
    protected $fillable = ['main_category_id', 'name', 'default_price', 'image', 'is_active'];
    protected $casts = ['default_price' => 'float', 'is_active' => 'boolean'];

    public function mainCategory(): BelongsTo
    {
        return $this->belongsTo(MainCategory::class);
    }

    public function animals(): HasMany
    {
        return $this->hasMany(Animal::class);
    }
}
