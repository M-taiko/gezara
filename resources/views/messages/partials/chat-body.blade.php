<!-- Chat Body -->
<div class="chat-body" id="ChatBody">
    @if($messages->count() > 0)
        @foreach($messages as $message)
            @include('messages.partials.message-item', [
                'message' => $message,
                'authUser' => $authUser
            ])
        @endforeach
    @else
        <div class="empty-state">
            <i class="fas fa-comments fa-3x"></i>
            <p>لا توجد رسائل بعد. ابدأ المحادثة!</p>
        </div>
    @endif
</div>
