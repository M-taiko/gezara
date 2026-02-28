<!-- Chat Header -->
<div class="chat-header">
    <div class="chat-header-left">
        <img
            src="{{ $user->profile && $user->profile->avatar ? asset($user->profile->avatar) : URL::asset('assets/img/faces/6.jpg') }}"
            alt="avatar"
            class="chat-header-avatar"
        >
        <div class="chat-header-info">
            <h6>{{ $user->name }}</h6>
            <small>
                @if($user->status === 'active')
                    Active now
                @else
                    Last seen {{ $user->updated_at->format('H:i') }} {{ $user->updated_at->format('d/m/Y') }}
                @endif
            </small>
        </div>
    </div>
    <div class="chat-header-actions">
        <button title="Call" type="button">
            <i class="fas fa-phone"></i>
        </button>
        <button title="Video call" type="button">
            <i class="fas fa-video"></i>
        </button>
        <button title="More options" type="button">
            <i class="fas fa-ellipsis-h"></i>
        </button>
    </div>
</div>
