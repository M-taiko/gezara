<!-- Typing Indicator -->
<div id="typingIndicator" class="typing-indicator">
    <span class="typing-dots">
        <span></span><span></span><span></span>
    </span>
</div>

<!-- Chat Footer with Message Input -->
<div class="chat-footer">
    <form
        id="messageForm"
        action="{{ route('messages.store') }}"
        method="POST"
        class="chat-form"
        data-conversation-id="{{ $conversation->id ?? 0 }}"
        data-current-user-id="{{ $authUser->id }}"
        data-other-user-id="{{ $user->id }}"
    >
        @csrf
        <input type="hidden" name="receiver_id" value="{{ $user->id }}">
        <input
            type="text"
            id="messageInput"
            name="content"
            placeholder="Type your message..."
            required
            autocomplete="off"
        >
        <button type="submit" title="Send message (Ctrl+Enter)">
            <i class="fas fa-paper-plane"></i>
        </button>
    </form>
</div>
