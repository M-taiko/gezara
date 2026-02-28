<?php

namespace Database\Seeders;

use App\Models\Message;
use App\Models\Conversation;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ConversationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get all unique user pairs from messages
        $messages = Message::select('sender_id', 'receiver_id')
            ->orderBy('created_at', 'desc')
            ->get();

        foreach ($messages as $message) {
            $userId1 = $message->sender_id;
            $userId2 = $message->receiver_id;

            // Find or create conversation
            $conversation = Conversation::findOrCreateBetween($userId1, $userId2);

            // Update last message time if not set
            if (is_null($conversation->last_message_at)) {
                $lastMsg = Message::where(function ($q) use ($userId1, $userId2) {
                    $q->where('sender_id', $userId1)->where('receiver_id', $userId2)
                      ->orWhere('sender_id', $userId2)->where('receiver_id', $userId1);
                })->latest()->first();

                if ($lastMsg) {
                    $conversation->update(['last_message_at' => $lastMsg->created_at]);
                }
            }
        }
    }
}
