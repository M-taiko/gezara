<?php

namespace App\Events;

use App\Models\Message;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class MessageEdited implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    use InteractsWithSockets;

    public function __construct(public Message $message)
    {
        $this->message->load('sender');
    }

    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('conversation.' . $this->message->conversation_id),
        ];
    }

    public function broadcastWith(): array
    {
        return [
            'message' => [
                'id' => $this->message->id,
                'content' => $this->message->content,
                'edited_at' => $this->message->edited_at->toISOString(),
                'original_content' => $this->message->original_content,
            ],
        ];
    }

    public function broadcastAs(): string
    {
        return 'message.edited';
    }
}
