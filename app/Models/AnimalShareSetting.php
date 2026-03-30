<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AnimalShareSetting extends Model
{
    const SHARE_TYPE_LABELS = [
        'seven'   => 'سُبع (7 أنصبة)',
        'six'     => 'سُدس (6 أنصبة)',
        'five'    => 'خُمس (5 أنصبة)',
        'quarter' => 'ربع (4 أنصبة)',
        'third'   => 'ثُلث (3 أنصبة)',
        'half'    => 'نصف (2 نصيب)',
    ];


    protected $fillable = ['animal_id', 'share_type', 'total_shares', 'sold_shares', 'remaining_shares'];
    protected $casts = ['total_shares' => 'integer', 'sold_shares' => 'integer', 'remaining_shares' => 'integer'];

    public function animal(): BelongsTo
    {
        return $this->belongsTo(Animal::class);
    }

    public function shareLabel(): string
    {
        return Animal::SHARE_LABELS[$this->share_type] ?? $this->share_type;
    }

    public function completionPercentage(): float
    {
        if ($this->total_shares === 0) return 0;
        return round(($this->sold_shares / $this->total_shares) * 100, 1);
    }
}
