<?php

use Illuminate\Support\Facades\Broadcast;
use App\Models\User;
use App\Models\Conversation;

/*
|--------------------------------------------------------------------------
| Broadcast Channels
|--------------------------------------------------------------------------
|
| Here you may register all of the event broadcasting channels that your
| application supports. The given channel authorization callbacks are
| used to check if an authenticated user can listen to the channel.
|
*/

/*
 * Private user channel for personal notifications
 * Users can only subscribe to their own user channel
 */
Broadcast::channel('user.{userId}', function (User $user, int $userId) {
    return (int) $user->id === (int) $userId;
});

/*
 * Private conversation channel for message events between two users
 * Users can only subscribe if they are part of the conversation
 */
Broadcast::channel('conversation.{conversationId}', function (User $user, int $conversationId) {
    $conversation = Conversation::find($conversationId);

    if (!$conversation) {
        return false;
    }

    return $conversation->user_one_id === $user->id
        || $conversation->user_two_id === $user->id;
});

/*
 * Typing indicator channel
 * Users can only broadcast their own typing status
 */
Broadcast::channel('typing.{userId}.{otherUserId}', function (User $user, int $userId, int $otherUserId) {
    return (int) $user->id === (int) $userId;
});
