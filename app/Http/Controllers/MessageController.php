<?php

namespace App\Http\Controllers;

use App\Models\Message;
use App\Models\User;
use App\Models\Conversation;
use App\Events\MessageSent;
use App\Events\MessageEdited;
use App\Events\MessageDeleted;
use App\Events\MessageRead;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MessageController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display all messages (conversations).
     */
    public function index()
    {
        $user = Auth::user();

        // Get all conversations for the user, sorted by most recent
        $conversations = Conversation::forUser($user->id)
            ->recent()
            ->with(['userOne', 'userTwo'])
            ->get();

        // Extract the other users from conversations
        $conversationUsers = $conversations->map(function ($conversation) use ($user) {
            return $conversation->getOtherUser($user);
        });

        return view('messages.index', compact('conversationUsers', 'user'));
    }

    /**
     * Show conversation with specific user.
     */
    public function show(User $user)
    {
        $authUser = Auth::user();

        if ($authUser->id === $user->id) {
            abort(403);
        }

        // Create or update conversation
        $conversation = Conversation::findOrCreateBetween($authUser->id, $user->id);

        // Get messages between current user and the specified user
        $messages = Message::where(function ($query) use ($authUser, $user) {
            $query->where('sender_id', $authUser->id)->where('receiver_id', $user->id)
                ->orWhere('sender_id', $user->id)->where('receiver_id', $authUser->id);
        })->orderBy('created_at', 'asc')->get();

        // Mark received messages as read
        Message::where('sender_id', $user->id)
            ->where('receiver_id', $authUser->id)
            ->where('is_read', false)
            ->update(['is_read' => true]);

        // Update last message time
        if ($messages->count() > 0) {
            $conversation->updateLastMessageTime();
        }

        // Get all conversations for the user, sorted by most recent
        $conversations = Conversation::forUser($authUser->id)
            ->recent()
            ->with(['userOne', 'userTwo'])
            ->get();

        // Extract the other users from conversations
        $conversationUsers = $conversations->map(function ($conv) use ($authUser) {
            return $conv->getOtherUser($authUser);
        });

        return view('messages.show', compact('user', 'messages', 'conversationUsers', 'authUser'));
    }

    /**
     * Store a new message.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'receiver_id' => 'required|exists:users,id|different:sender_id',
            'content' => 'required|string|min:1|max:5000',
        ]);

        // Create or update conversation first
        $conversation = Conversation::findOrCreateBetween(
            Auth::id(),
            $validated['receiver_id']
        );

        $message = Message::create([
            'sender_id' => Auth::id(),
            'receiver_id' => $validated['receiver_id'],
            'conversation_id' => $conversation->id,
            'content' => $validated['content'],
            'is_read' => false,
        ]);

        $conversation->updateLastMessageTime();

        // Load relationships for response
        $message->load('sender', 'sender.profile', 'reactions');

        // Broadcast event
        broadcast(new MessageSent($message))->toOthers();

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => $message,
            ]);
        }

        return back()->with('success', 'Message sent successfully!');
    }

    /**
     * Update a message.
     */
    public function update(Request $request, Message $message)
    {
        // Authorization
        if ($message->sender_id !== Auth::id()) {
            abort(403, 'Unauthorized');
        }

        // Validate
        $validated = $request->validate([
            'content' => 'required|string|min:1|max:5000',
        ]);

        // Store original content if first edit
        if (!$message->original_content) {
            $message->original_content = $message->content;
        }

        // Update message
        $message->update([
            'content' => $validated['content'],
            'edited_at' => now(),
        ]);

        // Broadcast event
        broadcast(new MessageEdited($message))->toOthers();

        return response()->json([
            'success' => true,
            'message' => $message,
        ]);
    }

    /**
     * Get unread messages count for current user.
     */
    public function unreadCount()
    {
        $count = Auth::user()->unreadMessagesCount();

        return response()->json([
            'unread_count' => $count,
        ]);
    }

    /**
     * Get recent messages for notification.
     */
    public function getRecentMessages()
    {
        $messages = Message::where('receiver_id', Auth::id())
            ->where('is_read', false)
            ->with('sender')
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        return response()->json([
            'messages' => $messages,
            'count' => $messages->count(),
        ]);
    }

    /**
     * Mark message as read.
     */
    public function markAsRead(Message $message)
    {
        if ($message->receiver_id !== Auth::id()) {
            abort(403);
        }

        $message->update(['is_read' => true]);

        // Broadcast read receipt event
        broadcast(new MessageRead($message, $message->conversation_id))->toOthers();

        return response()->json(['success' => true]);
    }

    /**
     * Delete a message (soft delete).
     */
    public function destroy(Message $message)
    {
        if ($message->sender_id !== Auth::id()) {
            abort(403);
        }

        $conversationId = $message->conversation_id;
        $messageId = $message->id;

        // Soft delete
        $message->delete();

        // Broadcast event
        broadcast(new MessageDeleted($messageId, $conversationId))->toOthers();

        if (request()->expectsJson()) {
            return response()->json(['success' => true]);
        }

        return back()->with('success', 'Message deleted successfully!');
    }
}
