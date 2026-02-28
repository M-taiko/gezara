<?php

namespace App\Events;

use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class MessageReactionRemoved implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    use InteractsWithSockets;

    public function __construct(
        public int $reactionId,
        public int $messageId,
        public int $userId,
        public string $reactionType,
        public int $conversationId
    ) {}

    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('conversation.' . $this->conversationId),
        ];
    }

    public function broadcastWith(): array
    {
        return [
            'reaction_id' => $this->reactionId,
            'message_id' => $this->messageId,
            'user_id' => $this->userId,
            'reaction_type' => $this->reactionType,
        ];
    }

    public function broadcastAs(): string
    {
        return 'message.reaction.removed';
    }
}
