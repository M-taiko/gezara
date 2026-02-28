<?php

namespace App\Events;

use App\Models\User;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class TypingStarted implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    use InteractsWithSockets;

    public function __construct(
        public User $user,
        public int $conversationId,
        public int $recipientId
    ) {}

    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('user.' . $this->recipientId),
        ];
    }

    public function broadcastWith(): array
    {
        return [
            'user_id' => $this->user->id,
            'user_name' => $this->user->name,
            'conversation_id' => $this->conversationId,
        ];
    }

    public function broadcastAs(): string
    {
        return 'typing.started';
    }
}
