<?php

namespace App\Http\Controllers;

use App\Models\Message;
use App\Models\MessageReaction;
use App\Events\MessageReactionAdded;
use App\Events\MessageReactionRemoved;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MessageReactionController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Add or toggle a reaction to a message
     */
    public function store(Request $request, Message $message)
    {
        $validated = $request->validate([
            'reaction_type' => 'required|in:like,love,haha,wow,sad,angry',
        ]);

        // Check if user already reacted with this type
        $existingReaction = MessageReaction::where('message_id', $message->id)
            ->where('user_id', Auth::id())
            ->where('reaction_type', $validated['reaction_type'])
            ->first();

        if ($existingReaction) {
            // Remove reaction (toggle off)
            $reactionId = $existingReaction->id;
            $existingReaction->delete();

            broadcast(new MessageReactionRemoved(
                $reactionId,
                $message->id,
                Auth::id(),
                $validated['reaction_type'],
                $message->conversation_id
            ))->toOthers();

            return response()->json([
                'success' => true,
                'action' => 'removed',
                'reaction_type' => $validated['reaction_type'],
            ]);
        }

        // Remove any other reaction types from this user on this message
        MessageReaction::where('message_id', $message->id)
            ->where('user_id', Auth::id())
            ->delete();

        // Add new reaction
        $reaction = MessageReaction::create([
            'message_id' => $message->id,
            'user_id' => Auth::id(),
            'reaction_type' => $validated['reaction_type'],
        ]);

        $reaction->load('user');

        // Broadcast event
        broadcast(new MessageReactionAdded($reaction, $message->conversation_id))->toOthers();

        return response()->json([
            'success' => true,
            'action' => 'added',
            'reaction' => $reaction,
        ]);
    }

    /**
     * Remove a reaction
     */
    public function destroy(Message $message, $reactionType)
    {
        $reaction = MessageReaction::where('message_id', $message->id)
            ->where('user_id', Auth::id())
            ->where('reaction_type', $reactionType)
            ->first();

        if (!$reaction) {
            return response()->json(['success' => false, 'message' => 'Reaction not found'], 404);
        }

        $reactionId = $reaction->id;
        $reaction->delete();

        broadcast(new MessageReactionRemoved(
            $reactionId,
            $message->id,
            Auth::id(),
            $reactionType,
            $message->conversation_id
        ))->toOthers();

        return response()->json(['success' => true]);
    }
}
