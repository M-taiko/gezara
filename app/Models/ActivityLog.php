<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Builder;

class ActivityLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'action',
        'model_type',
        'model_id',
        'description',
        'ip_address',
        'user_agent',
        'changes',
    ];

    protected $casts = [
        'changes' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the user that performed the action.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Scope: Filter logs for a specific user.
     */
    public function scopeForUser(Builder $query, $userId): Builder
    {
        return $query->where('user_id', $userId);
    }

    /**
     * Scope: Filter logs for a specific model type and ID.
     */
    public function scopeForModel(Builder $query, $modelType, $modelId): Builder
    {
        return $query->where('model_type', $modelType)->where('model_id', $modelId);
    }

    /**
     * Scope: Get recent logs first.
     */
    public function scopeRecent(Builder $query): Builder
    {
        return $query->orderByDesc('created_at');
    }

    /**
     * Scope: Filter by action type.
     */
    public function scopeByAction(Builder $query, $action): Builder
    {
        return $query->where('action', $action);
    }

    /**
     * Scope: Filter by date range.
     */
    public function scopeDateBetween(Builder $query, $startDate, $endDate): Builder
    {
        return $query->whereBetween('created_at', [$startDate, $endDate]);
    }

    /**
     * Get a human-readable description of changes.
     */
    public function getChangesDescription(): string
    {
        if (!$this->changes || empty($this->changes)) {
            return 'No changes recorded';
        }

        $changes = $this->changes;
        $description = 'Changes: ';

        if (isset($changes['before']) && isset($changes['after'])) {
            $before = $changes['before'];
            $after = $changes['after'];

            $fields = [];
            foreach ($after as $field => $value) {
                if (isset($before[$field]) && $before[$field] !== $value) {
                    $fields[] = "$field: '{$before[$field]}' → '{$value}'";
                } elseif (!isset($before[$field])) {
                    $fields[] = "$field: '{$value}'";
                }
            }

            $description .= implode(', ', $fields);
        }

        return $description;
    }
}
