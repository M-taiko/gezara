<?php

namespace App\Events;

use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class TypingStopped implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    use InteractsWithSockets;

    public function __construct(
        public int $userId,
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
            'user_id' => $this->userId,
            'conversation_id' => $this->conversationId,
        ];
    }

    public function broadcastAs(): string
    {
        return 'typing.stopped';
    }
}
