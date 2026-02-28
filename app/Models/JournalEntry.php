<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class JournalEntry extends Model
{
    protected $fillable = ['reference_type', 'reference_id', 'description', 'date'];
    protected $casts    = ['date' => 'date'];

    public function items(): HasMany
    {
        return $this->hasMany(JournalEntryItem::class);
    }

    public function totalDebits(): float
    {
        return (float) $this->items()->where('type', 'debit')->sum('amount');
    }

    public function totalCredits(): float
    {
        return (float) $this->items()->where('type', 'credit')->sum('amount');
    }

    public function isBalanced(): bool
    {
        return abs($this->totalDebits() - $this->totalCredits()) < 0.01;
    }
}
