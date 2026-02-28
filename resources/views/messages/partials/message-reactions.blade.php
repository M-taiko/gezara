<!-- Message Reactions -->
<div class="message-reactions" data-message-reactions="{{ $message->id }}">
    @if($message->reactions->count() > 0)
        @php
            $reactionGroups = $message->reactionsGrouped();
            $reactionIcons = [
                'like' => '👍',
                'love' => '❤️',
                'haha' => '😄',
                'wow' => '😮',
                'sad' => '😢',
                'angry' => '😠'
            ];
        @endphp
        <div class="reaction-bubbles">
            @foreach($reactionGroups as $type => $data)
                <span class="reaction-bubble" data-reaction="{{ $type }}">
                    {{ $reactionIcons[$type] }} {{ $data->count }}
                </span>
            @endforeach
        </div>
    @endif
</div>
