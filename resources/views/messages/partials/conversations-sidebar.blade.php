<!-- Conversations Sidebar -->
<div class="col-xl-3 col-lg-4">
    <div class="card h-100">
        <div class="card-header bg-light">
            <h6 class="card-title mb-0">
                <i class="fas fa-comments mr-2"></i>المحادثات ({{ $conversationUsers->count() }})
            </h6>
        </div>
        <div class="card-body p-0 conversations-sidebar">
            @if($conversationUsers->count() > 0)
                @foreach($conversationUsers as $conversationUser)
                    <a
                        href="{{ route('messages.show', $conversationUser) }}"
                        class="conversation-item @if($conversationUser->id === $user->id) selected @endif"
                        title="{{ $conversationUser->name }}"
                    >
                        <img
                            src="{{ $conversationUser->profile && $conversationUser->profile->avatar ? asset($conversationUser->profile->avatar) : URL::asset('assets/img/faces/6.jpg') }}"
                            alt="avatar"
                            class="conversation-avatar @if($conversationUser->status === 'active') online @endif"
                        >
                        <div class="conversation-info">
                            <h6>{{ $conversationUser->name }}</h6>
                            <small>
                                @php
                                    $lastMsg = \App\Models\Message::where(function ($q) use ($authUser, $conversationUser) {
                                        $q->where('sender_id', $authUser->id)->where('receiver_id', $conversationUser->id)
                                          ->orWhere('sender_id', $conversationUser->id)->where('receiver_id', $authUser->id);
                                    })->latest()->first();
                                @endphp
                                @if($lastMsg)
                                    {{ substr($lastMsg->content, 0, 30) }}{{ strlen($lastMsg->content) > 30 ? '...' : '' }}
                                @else
                                    لا توجد رسائل بعد
                                @endif
                            </small>
                        </div>
                        @if($conversationUser->status === 'active')
                            <span class="badge badge-success badge-sm conversation-badge">متصل</span>
                        @endif
                    </a>
                @endforeach
            @else
                <div class="p-4 text-center text-muted">
                    <i class="fas fa-inbox fa-2x mb-2"></i>
                    <p>لا توجد محادثات بعد</p>
                </div>
            @endif
        </div>
    </div>
</div>
