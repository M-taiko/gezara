<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Animal extends Model
{
    const SHARE_MAP = ['seven' => 7, 'five' => 5, 'quarter' => 4, 'half' => 2];

    const STATUS_LABELS = [
        'available'           => 'متاح',
        'partially_allocated' => 'مباع جزئياً',
        'fully_allocated'     => 'مباع كلياً',
        'slaughtered'         => 'مذبوح',
    ];

    const STATUS_COLORS = [
        'available'           => 'success',
        'partially_allocated' => 'warning',
        'fully_allocated'     => 'danger',
        'slaughtered'         => 'secondary',
    ];

    const SHARE_LABELS = [
        'seven'   => 'سبع (7 أنصبة)',
        'five'    => 'خمس (5 أنصبة)',
        'quarter' => 'ربع (4 أنصبة)',
        'half'    => 'نصف (2 نصيب)',
    ];

    protected $fillable = [
        'product_id', 'supplier_id', 'purchase_id', 'warehouse_id',
        'code', 'weight', 'cost', 'is_grouped', 'status',
        'price_full', 'price_seven', 'price_five', 'price_quarter', 'price_half', 'notes',
    ];

    protected $casts = [
        'weight' => 'float', 'cost' => 'float', 'is_grouped' => 'boolean',
        'price_full' => 'float', 'price_seven' => 'float',
        'price_five' => 'float', 'price_quarter' => 'float', 'price_half' => 'float',
    ];

    // ─── Auto-generate code on create ───────────────────────────────────────
    protected static function booted(): void
    {
        static::creating(function (self $animal) {
            if (empty($animal->code)) {
                $animal->code = self::generateCode(
                    $animal->product->mainCategory->code
                );
            }
        });
    }

    private static function generateCode(string $categoryCode): string
    {
        $year     = date('Y');
        $prefix   = $year . '-' . $categoryCode . '-';
        $last     = self::where('code', 'like', $prefix . '%')->count();
        return $prefix . str_pad($last + 1, 4, '0', STR_PAD_LEFT);
    }

    // ─── Relationships ───────────────────────────────────────────────────────
    public function product(): BelongsTo    { return $this->belongsTo(Product::class); }
    public function supplier(): BelongsTo   { return $this->belongsTo(Supplier::class); }
    public function purchase(): BelongsTo   { return $this->belongsTo(Purchase::class); }
    public function warehouse(): BelongsTo  { return $this->belongsTo(Warehouse::class); }
    public function shareSetting(): HasOne  { return $this->hasOne(AnimalShareSetting::class); }
    public function contractItems(): HasMany { return $this->hasMany(ContractItem::class); }
    public function transfers(): HasMany    { return $this->hasMany(AnimalWarehouseTransfer::class); }

    // ─── Business Logic ──────────────────────────────────────────────────────
    public function canSellFull(): bool
    {
        return $this->status === 'available' && !$this->is_grouped;
    }

    public function canSellShares(): bool
    {
        return $this->is_grouped
            && in_array($this->status, ['available', 'partially_allocated']);
    }

    public function statusLabel(): string
    {
        return self::STATUS_LABELS[$this->status] ?? $this->status;
    }

    public function statusColor(): string
    {
        return self::STATUS_COLORS[$this->status] ?? 'secondary';
    }

    public function categoryName(): string
    {
        return $this->product->mainCategory->name ?? '—';
    }
}
