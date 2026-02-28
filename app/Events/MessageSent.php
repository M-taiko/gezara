<?php

namespace App\Events;

use App\Models\Message;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class MessageSent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(public Message $message)
    {
        // Load relationships for broadcasting
        $this->message->load('sender', 'sender.profile', 'reactions');
    }

    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('user.' . $this->message->receiver_id),
            new PrivateChannel('conversation.' . $this->message->conversation_id),
        ];
    }

    public function broadcastWith(): array
    {
        return [
            'message' => [
                'id' => $this->message->id,
                'sender_id' => $this->message->sender_id,
                'receiver_id' => $this->message->receiver_id,
                'conversation_id' => $this->message->conversation_id,
                'content' => $this->message->content,
                'is_read' => $this->message->is_read,
                'created_at' => $this->message->created_at->toISOString(),
                'sender' => [
                    'id' => $this->message->sender->id,
                    'name' => $this->message->sender->name,
                    'avatar' => $this->message->sender->profile?->avatar ?? null,
                ],
                'reactions' => $this->message->reactions,
            ],
        ];
    }

    public function broadcastAs(): string
    {
        return 'message.sent';
    }
}
