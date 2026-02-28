<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SlaughterGroup extends Model
{
    const SHARE_MAP = ['seven' => 7, 'five' => 5, 'quarter' => 4, 'half' => 2, 'full' => 1];

    const SHARE_LABELS = [
        'seven'   => 'سُبع (7 أنصبة)',
        'five'    => 'خُمس (5 أنصبة)',
        'quarter' => 'ربع (4 أنصبة)',
        'half'    => 'نصف (2 نصيب)',
        'full'    => 'كامل',
    ];

    protected $fillable = ['name', 'animal_id', 'share_type', 'slaughter_day', 'notes'];

    protected $casts = ['slaughter_day' => 'date'];

    public function animal(): BelongsTo
    {
        return $this->belongsTo(Animal::class);
    }

    public function members(): HasMany
    {
        return $this->hasMany(SlaughterGroupMember::class, 'group_id');
    }

    public function totalSlots(): int
    {
        return self::SHARE_MAP[$this->share_type] ?? 1;
    }

    public function usedSlots(): int
    {
        return (int) $this->members->sum('shares_count');
    }

    public function remainingSlots(): int
    {
        return $this->totalSlots() - $this->usedSlots();
    }

    public function shareLabel(): string
    {
        return self::SHARE_LABELS[$this->share_type] ?? $this->share_type;
    }
}
