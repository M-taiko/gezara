<?php

namespace App\Http\Controllers;

use App\Models\Conversation;
use App\Events\TypingStarted;
use App\Events\TypingStopped;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class TypingIndicatorController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Broadcast that user started typing
     */
    public function start(Request $request)
    {
        $validated = $request->validate([
            'conversation_id' => 'required|exists:conversations,id',
            'recipient_id' => 'required|exists:users,id',
        ]);

        $conversation = Conversation::find($validated['conversation_id']);

        // Verify user is part of conversation
        if ($conversation->user_one_id !== Auth::id() && $conversation->user_two_id !== Auth::id()) {
            abort(403);
        }

        // Cache typing state (10 second TTL)
        $cacheKey = "typing:{$validated['conversation_id']}:" . Auth::id();
        Cache::put($cacheKey, now()->timestamp, 10);

        // Broadcast event
        broadcast(new TypingStarted(
            Auth::user(),
            $validated['conversation_id'],
            $validated['recipient_id']
        ))->toOthers();

        return response()->json(['success' => true]);
    }

    /**
     * Broadcast that user stopped typing
     */
    public function stop(Request $request)
    {
        $validated = $request->validate([
            'conversation_id' => 'required|exists:conversations,id',
            'recipient_id' => 'required|exists:users,id',
        ]);

        // Clear cache
        $cacheKey = "typing:{$validated['conversation_id']}:" . Auth::id();
        Cache::forget($cacheKey);

        // Broadcast event
        broadcast(new TypingStopped(
            Auth::id(),
            $validated['conversation_id'],
            $validated['recipient_id']
        ))->toOthers();

        return response()->json(['success' => true]);
    }
}
