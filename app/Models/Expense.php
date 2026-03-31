<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Expense extends Model
{
    const CATEGORIES = [
        'feed'      => ['label' => 'علف وتغذية',    'emoji' => '🌾'],
        'salary'    => ['label' => 'مرتبات',         'emoji' => '💼'],
        'treatment' => ['label' => 'علاج وبيطري',    'emoji' => '💊'],
        'transport' => ['label' => 'نقل ومواصلات',   'emoji' => '🚛'],
        'rent'      => ['label' => 'إيجار',          'emoji' => '🏠'],
        'other'     => ['label' => 'مصروفات أخرى',  'emoji' => '📦'],
    ];

    protected $fillable = ['animal_id', 'category', 'description', 'amount', 'date', 'notes'];
    protected $casts    = ['amount' => 'float', 'date' => 'date'];

    public function animal(): BelongsTo { return $this->belongsTo(Animal::class); }

    public function categoryLabel(): string
    {
        return self::CATEGORIES[$this->category]['label'] ?? $this->category;
    }

    public function categoryEmoji(): string
    {
        return self::CATEGORIES[$this->category]['emoji'] ?? '📦';
    }
}
