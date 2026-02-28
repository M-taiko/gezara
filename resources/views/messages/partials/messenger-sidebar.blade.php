<!-- Facebook Messenger Sidebar with Search -->
<div class="messenger-sidebar-header">
    <h2 class="messenger-title">الرسائل</h2>
    <div class="messenger-icons">
        <button class="messenger-icon-btn" title="رسالة جديدة">
            <i class="fas fa-pen-to-square"></i>
        </button>
    </div>
</div>

<!-- Search Bar -->
<div class="messenger-search-container">
    <div class="messenger-search-wrapper">
        <i class="fas fa-search"></i>
        <input
            type="text"
            class="messenger-search"
            id="conversationSearch"
            placeholder="ابحث في المحادثات..."
        >
    </div>
</div>

<!-- Conversations List -->
<div class="messenger-conversations">
    @if($conversationUsers->count() > 0)
        @foreach($conversationUsers as $conversationUser)
            <a
                href="{{ route('messages.show', $conversationUser) }}"
                class="messenger-conversation-item @if($conversationUser->id === $user->id) active @endif"
                data-user-name="{{ strtolower($conversationUser->name) }}"
                title="{{ $conversationUser->name }}"
            >
                <!-- Avatar with Online Status -->
                <div class="conversation-avatar-wrapper">
                    <img
                        src="{{ $conversationUser->profile && $conversationUser->profile->avatar ? asset($conversationUser->profile->avatar) : URL::asset('assets/img/faces/6.jpg') }}"
                        alt="avatar"
                        class="conversation-avatar-img"
                    >
                    @if($conversationUser->status === 'active')
                        <span class="online-badge"></span>
                    @endif
                </div>

                <!-- Conversation Info -->
                <div class="conversation-info-wrapper">
                    <div class="conversation-header">
                        <h5 class="conversation-name">{{ $conversationUser->name }}</h5>
                        <span class="conversation-time">
                            @php
                                $lastMsg = \App\Models\Message::where(function ($q) use ($authUser, $conversationUser) {
                                    $q->where('sender_id', $authUser->id)->where('receiver_id', $conversationUser->id)
                                      ->orWhere('sender_id', $conversationUser->id)->where('receiver_id', $authUser->id);
                                })->latest()->first();
                            @endphp
                            @if($lastMsg)
                                {{ $lastMsg->created_at->diffForHumans() }}
                            @endif
                        </span>
                    </div>
                    <div class="conversation-preview">
                        @php
                            $lastMsg = \App\Models\Message::where(function ($q) use ($authUser, $conversationUser) {
                                $q->where('sender_id', $authUser->id)->where('receiver_id', $conversationUser->id)
                                  ->orWhere('sender_id', $conversationUser->id)->where('receiver_id', $authUser->id);
                            })->latest()->first();
                        @endphp
                        @if($lastMsg)
                            <span class="preview-text">
                                @if($lastMsg->sender_id === $authUser->id)
                                    <strong>أنت:</strong>
                                @endif
                                {{ substr($lastMsg->content, 0, 40) }}{{ strlen($lastMsg->content) > 40 ? '...' : '' }}
                            </span>
                        @else
                            <span class="preview-text empty">لا توجد رسائل بعد</span>
                        @endif
                    </div>
                </div>
            </a>
        @endforeach
    @else
        <div class="empty-conversations">
            <i class="fas fa-inbox"></i>
            <p>لا توجد محادثات بعد</p>
        </div>
    @endif
</div>

<script>
    // Search functionality
    document.getElementById('conversationSearch').addEventListener('keyup', function(e) {
        const searchTerm = e.target.value.toLowerCase();
        const conversations = document.querySelectorAll('.messenger-conversation-item');

        conversations.forEach(conv => {
            const userName = conv.dataset.userName;
            if (userName.includes(searchTerm)) {
                conv.style.display = 'flex';
            } else {
                conv.style.display = 'none';
            }
        });
    });
</script>
