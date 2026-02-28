@extends('layouts.master')

@section('css')
    @include('messages.partials.chat-styles')
@endsection

@section('page-header')
<div class="breadcrumb-header justify-content-between">
    <div class="my-auto">
        <div class="d-flex">
            <h4 class="content-title mb-0 my-auto">Messages</h4>
            <span class="text-muted mt-1 tx-13 mr-2 mb-0">/ {{ $user->name }}</span>
        </div>
    </div>
    <div class="d-flex my-xl-auto right-content">
        <div class="pr-1 mb-3 mb-xl-0">
            <a href="{{ route('messages.index') }}" class="btn btn-secondary btn-icon ml-2">
                <i class="mdi mdi-arrow-left"></i> Back to Messages
            </a>
        </div>
    </div>
</div>
@endsection

@section('content')
<div class="row row-sm">
    {{-- Conversations Sidebar --}}
    @include('messages.partials.conversations-sidebar', [
        'conversationUsers' => $conversationUsers,
        'authUser' => $authUser,
        'user' => $user
    ])

    {{-- Chat Container --}}
    <div class="col-xl-9 col-lg-8">
        <div class="card chat-container">
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
    </div>
</div>
@endsection

@section('js')
<script>
    $(function() {
        'use strict';

        let isSubmitting = false;

        // Auto-scroll to bottom on load
        function scrollToBottom() {
            const chatBody = document.getElementById('ChatBody');
            if (chatBody) {
                chatBody.scrollTop = chatBody.scrollHeight;
            }
        }

        // Initialize on page load
        scrollToBottom();

        // Handle form submission with AJAX
        $('#messageForm').on('submit', function(e) {
            e.preventDefault();

            // Prevent double submission
            if (isSubmitting) {
                return;
            }

            const content = $('#messageInput').val().trim();

            if (!content) return;

            isSubmitting = true;
            const $button = $(this).find('button[type="submit"]');
            $button.prop('disabled', true);

            $.ajax({
                url: $(this).attr('action'),
                method: 'POST',
                data: $(this).serialize(),
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    // Clear input
                    $('#messageInput').val('');

                    // Optionally process response
                    if (response.success) {
                        // Message will be added via WebSocket broadcast
                    }
                },
                error: function(xhr) {
                    alert('Error sending message. Please try again.');
                },
                complete: function() {
                    isSubmitting = false;
                    $button.prop('disabled', false);
                    $('#messageInput').focus();
                }
            });
        });

        // Focus input on load
        $('#messageInput').focus();
    });
</script>

{{-- Real-time messaging with Laravel Echo and Reverb --}}
<script src="{{ asset('js/messages-realtime.js') }}"></script>
@endsection
