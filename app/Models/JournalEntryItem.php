<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class JournalEntryItem extends Model
{
    protected $fillable = ['journal_entry_id', 'account_id', 'type', 'amount', 'description'];
    protected $casts    = ['amount' => 'float'];

    public function journalEntry(): BelongsTo { return $this->belongsTo(JournalEntry::class); }
    public function account(): BelongsTo      { return $this->belongsTo(Account::class); }

    public function typeLabel(): string
    {
        return $this->type === 'debit' ? 'مدين' : 'دائن';
    }
}
