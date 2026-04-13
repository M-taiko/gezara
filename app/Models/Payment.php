<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Payment extends Model
{
    const METHOD_LABELS = ['cash' => 'نقدي', 'bank' => 'بنك', 'transfer' => 'تحويل'];

    protected $fillable = ['contract_id', 'amount', 'payment_method', 'receipt_number', 'date', 'notes', 'wallet_id'];
    protected $casts = ['amount' => 'float', 'date' => 'date'];

    protected static function booted(): void
    {
        static::creating(function (Payment $payment) {
            if (empty($payment->receipt_number)) {
                $year = now()->year;
                $last = static::where('receipt_number', 'like', "RCP-{$year}-%")
                    ->lockForUpdate()
                    ->max('receipt_number');
                $next = $last ? ((int) substr($last, -4)) + 1 : 1;
                $payment->receipt_number = 'RCP-' . $year . '-' . str_pad($next, 4, '0', STR_PAD_LEFT);
            }
        });
    }

    public function contract(): BelongsTo { return $this->belongsTo(Contract::class); }

    public function wallet(): BelongsTo { return $this->belongsTo(Wallet::class); }

    public function methodLabel(): string
    {
        return self::METHOD_LABELS[$this->payment_method] ?? $this->payment_method;
    }
}
