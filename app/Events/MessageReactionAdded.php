<?php

namespace App\Events;

use App\Models\MessageReaction;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class MessageReactionAdded implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    use InteractsWithSockets;

    public function __construct(
        public MessageReaction $reaction,
        public int $conversationId
    ) {
        $this->reaction->load('user', 'message');
    }

    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('conversation.' . $this->conversationId),
        ];
    }

    public function broadcastWith(): array
    {
        return [
            'reaction' => [
                'id' => $this->reaction->id,
                'message_id' => $this->reaction->message_id,
                'user_id' => $this->reaction->user_id,
                'reaction_type' => $this->reaction->reaction_type,
                'user' => [
                    'id' => $this->reaction->user->id,
                    'name' => $this->reaction->user->name,
                ],
            ],
        ];
    }

    public function broadcastAs(): string
    {
        return 'message.reaction.added';
    }
}
