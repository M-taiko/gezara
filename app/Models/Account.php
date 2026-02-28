<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Account extends Model
{
    const TYPE_LABELS = [
        'asset'     => 'أصول',
        'liability' => 'خصوم',
        'revenue'   => 'إيرادات',
        'expense'   => 'مصروفات',
        'equity'    => 'حقوق الملكية',
    ];

    // كودات الحسابات النظامية
    const TREASURY        = '1000';
    const INVENTORY       = '1100';
    const RECEIVABLES     = '3000';
    const PAYABLES        = '2000';
    const SALES_REVENUE   = '4000';
    const COGS            = '5000';

    protected $fillable = ['code', 'name', 'type', 'is_system', 'balance'];
    protected $casts    = ['is_system' => 'boolean', 'balance' => 'float'];

    public function journalItems(): HasMany
    {
        return $this->hasMany(JournalEntryItem::class);
    }

    public function typeLabel(): string
    {
        return self::TYPE_LABELS[$this->type] ?? $this->type;
    }

    public static function findByCode(string $code): self
    {
        return self::where('code', $code)->firstOrFail();
    }
}
