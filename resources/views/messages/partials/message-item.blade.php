<!-- Message Item -->
<div class="message-group @if($message->sender_id === $authUser->id) sent @else received @endif" data-message-id="{{ $message->id }}">
    {{-- Avatar for received messages --}}
    @if($message->sender_id !== $authUser->id)
        <img
            src="{{ $message->sender->profile && $message->sender->profile->avatar ? asset($message->sender->profile->avatar) : URL::asset('assets/img/faces/6.jpg') }}"
            alt="avatar"
            class="message-avatar"
        >
    @endif

    <div class="message-content">
        {{-- Message Bubble --}}
        <div class="message-bubble @if($message->sender_id === $authUser->id) sent @else received @endif" data-message-content="{{ $message->id }}">
            {{ $message->content }}
            @if($message->isEdited())
                <span class="edited-label">(تم التعديل)</span>
            @endif
        </div>

        {{-- Message Meta (Time & Status) --}}
        <div class="message-meta">
            <span class="message-time">{{ $message->created_at->format('h:i A') }}</span>
            @if($message->sender_id === $authUser->id)
                <span class="message-status @if($message->is_read) read @else unread @endif" data-message-status="{{ $message->id }}">
                    @if($message->is_read)
                        <i class="fas fa-check-double checkmark"></i>
                    @else
                        <i class="fas fa-check checkmark"></i>
                    @endif
                </span>
            @endif
        </div>

        {{-- Message Reactions --}}
        @include('messages.partials.message-reactions', ['message' => $message])

        {{-- Message Actions (Edit, Delete, React) --}}
        @if($message->sender_id === $authUser->id)
            @include('messages.partials.message-actions', ['message' => $message])
        @endif
    </div>
</div>
