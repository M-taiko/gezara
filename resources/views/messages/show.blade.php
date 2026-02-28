@extends('layouts.master')

@section('css')
    @include('messages.partials.chat-styles-fb')
@endsection

@section('content')
<div class="messenger-wrapper">
    <!-- Facebook Messenger Style Layout -->
    <div class="messenger-container">
        <!-- Left Sidebar - Conversations List -->
        <div class="messenger-sidebar">
            @include('messages.partials.messenger-sidebar', [
                'conversationUsers' => $conversationUsers,
                'authUser' => $authUser,
                'user' => $user
            ])
        </div>

        <!-- Right Chat Area -->
        <div class="messenger-chat">
            @if($user)
                <div class="chat-container">
                    {{-- Chat Header --}}
                    @include('messages.partials.chat-header', ['user' => $user])

                    {{-- Chat Body --}}
                    @include('messages.partials.chat-body', [
                        'messages' => $messages,
                        'authUser' => $authUser
                    ])

                    {{-- Chat Footer --}}
                    @include('messages.partials.chat-footer', [
                        'conversation' => $conversation ?? null,
                        'authUser' => $authUser,
                        'user' => $user
                    ])
                </div>
            @else
                <div class="chat-empty-state">
                    <i class="fas fa-comments"></i>
                    <h3>اختر محادثة</h3>
                    <p>اختر شخصاً للبدء في الدردشة</p>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection

@section('js')
<script>
    $(function() {
        'use strict';

        // Auto-scroll to bottom on load
        function scrollToBottom() {
            const chatBody = document.getElementById('ChatBody');
            if (chatBody) {
                chatBody.scrollTop = chatBody.scrollHeight;
            }
        }

        // Initialize on page load
        scrollToBottom();

        // Focus input on load
        $('#messageInput').focus();
    });
</script>

{{-- Real-time messaging with Laravel Echo and Reverb --}}
{{-- Note: Form submission is handled entirely by messages-realtime.js --}}
<script src="{{ asset('js/messages-realtime.js') }}"></script>
@endsection
