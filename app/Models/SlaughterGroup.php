<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SlaughterGroup extends Model
{
    const SHARE_MAP = ['seven' => 7, 'six' => 6, 'five' => 5, 'quarter' => 4, 'third' => 3, 'half' => 2, 'full' => 1];

    const SHARE_LABELS = [
        'seven'   => 'سُبع (7 أنصبة)',
        'six'     => 'سُدس (6 أنصبة)',
        'five'    => 'خُمس (5 أنصبة)',
        'quarter' => 'ربع (4 أنصبة)',
        'third'   => 'ثُلث (3 أنصبة)',
        'half'    => 'نصف (2 نصيب)',
        'full'    => 'كامل',
    ];

    protected $fillable = ['name', 'animal_id', 'animal_type_label', 'share_type', 'slaughter_day', 'notes', 'updated_by_user_id', 'edit_history'];

    protected $casts = ['slaughter_day' => 'date', 'edit_history' => 'array'];

    public function animal(): BelongsTo
    {
        return $this->belongsTo(Animal::class);
    }

    public function members(): HasMany
    {
        return $this->hasMany(SlaughterGroupMember::class, 'group_id');
    }

    public function updatedByUser(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class, 'updated_by_user_id');
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

    public function addEditHistory(string $action, ?string $details = null): void
    {
        $history = $this->edit_history ?? [];
        $history[] = [
            'action' => $action,
            'details' => $details,
            'user_id' => auth()->id(),
            'user_name' => auth()->user()->name ?? 'Unknown',
            'timestamp' => now()->toDateTimeString(),
        ];
        $this->update([
            'edit_history' => $history,
            'updated_by_user_id' => auth()->id(),
        ]);
    }

    public function isSlaughtered(): bool
    {
        return $this->animal?->status === 'slaughtered';
    }
}
