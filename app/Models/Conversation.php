<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Builder;

class Conversation extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_one_id',
        'user_two_id',
        'last_message_at',
    ];

    protected $casts = [
        'last_message_at' => 'datetime',
    ];

    /**
     * Get the first user in the conversation
     */
    public function userOne(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_one_id');
    }

    /**
     * Get the second user in the conversation
     */
    public function userTwo(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_two_id');
    }

    /**
     * Get the other user in the conversation (not the current user)
     */
    public function getOtherUser(User $user): User
    {
        if ($this->user_one_id === $user->id) {
            return $this->userTwo;
        }

        return $this->userOne;
    }

    /**
     * Scope: Get conversations for a specific user
     */
    public function scopeForUser(Builder $query, $userId): Builder
    {
        return $query->where(function ($q) use ($userId) {
            $q->where('user_one_id', $userId)
              ->orWhere('user_two_id', $userId);
        });
    }

    /**
     * Scope: Get conversations sorted by most recent
     */
    public function scopeRecent(Builder $query): Builder
    {
        return $query->orderByDesc('last_message_at');
    }

    /**
     * Find or create a conversation between two users
     */
    public static function findOrCreateBetween($userOneId, $userTwoId): self
    {
        // Ensure user_one_id is always less than user_two_id for consistency
        if ($userOneId > $userTwoId) {
            [$userOneId, $userTwoId] = [$userTwoId, $userOneId];
        }

        return self::firstOrCreate([
            'user_one_id' => $userOneId,
            'user_two_id' => $userTwoId,
        ]);
    }

    /**
     * Update the last message timestamp
     */
    public function updateLastMessageTime(): void
    {
        $this->update([
            'last_message_at' => now(),
        ]);
    }

    /**
     * Get all messages in this conversation
     */
    public function messages(): HasMany
    {
        return $this->hasMany(Message::class);
    }

    /**
     * Get the latest message in this conversation
     */
    public function latestMessage()
    {
        return $this->hasOne(Message::class)->latestOfMany();
    }

    /**
     * Get unread message count for a specific user in this conversation
     */
    public function unreadCountForUser(User $user): int
    {
        return $this->messages()
            ->where('receiver_id', $user->id)
            ->where('is_read', false)
            ->count();
    }
}
