<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Contract extends Model
{
    const STATUS_LABELS = ['active' => 'نشط', 'completed' => 'مكتمل', 'cancelled' => 'ملغي'];
    const STATUS_COLORS = ['active' => 'primary', 'completed' => 'success', 'cancelled' => 'danger'];

    protected $fillable = [
        'customer_id', 'contract_number', 'slaughter_day', 'slaughter_order',
        'notes', 'total_amount', 'paid_amount', 'remaining_amount', 'status',
    ];
    protected $casts = [
        'slaughter_day' => 'date',
        'total_amount' => 'float', 'paid_amount' => 'float', 'remaining_amount' => 'float',
    ];

    // ─── Auto-generate contract_number ──────────────────────────────────────
    protected static function booted(): void
    {
        static::creating(function (self $contract) {
            if (empty($contract->contract_number)) {
                $year = date('Y');
                $last = self::where('contract_number', 'like', "CNT-{$year}-%")
                    ->lockForUpdate()
                    ->max('contract_number');
                $next = $last ? ((int) substr($last, -4)) + 1 : 1;
                $contract->contract_number = 'CNT-' . $year . '-' . str_pad($next, 4, '0', STR_PAD_LEFT);
            }
        });
    }

    public function customer(): BelongsTo { return $this->belongsTo(Customer::class); }
    public function items(): HasMany      { return $this->hasMany(ContractItem::class); }
    public function payments(): HasMany   { return $this->hasMany(Payment::class); }

    public function statusLabel(): string { return self::STATUS_LABELS[$this->status] ?? $this->status; }
    public function statusColor(): string { return self::STATUS_COLORS[$this->status] ?? 'secondary'; }

    public function isPaid(): bool { return $this->remaining_amount <= 0; }
}
