<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Payment extends Model
{
    const METHOD_LABELS = ['cash' => 'نقدي', 'bank' => 'بنك', 'transfer' => 'تحويل'];

    protected $fillable = ['contract_id', 'amount', 'payment_method', 'receipt_number', 'date', 'notes'];
    protected $casts = ['amount' => 'float', 'date' => 'date'];

    public function contract(): BelongsTo { return $this->belongsTo(Contract::class); }

    public function methodLabel(): string
    {
        return self::METHOD_LABELS[$this->payment_method] ?? $this->payment_method;
    }
}
