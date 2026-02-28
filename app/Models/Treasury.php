<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Treasury extends Model
{
    protected $fillable = ['type', 'amount', 'reference_type', 'reference_id', 'description', 'date'];
    protected $casts = ['amount' => 'float', 'date' => 'date'];

    /** الرصيد الإجمالي */
    public static function balance(): float
    {
        $in  = (float) self::where('type', 'in')->sum('amount');
        $out = (float) self::where('type', 'out')->sum('amount');
        return $in - $out;
    }
}
