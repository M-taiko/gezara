/**
 * Messages Notification System
 * Handles real-time message notifications and unread count updates
 */

$(function() {
    'use strict';

    // Update messages on page load
    updateMessagesNotification();

    // Update messages every 5 seconds
    setInterval(updateMessagesNotification, 5000);

    /**
     * Update message notifications in top bar
     */
    function updateMessagesNotification() {
        $.ajax({
            url: '/api/messages/recent',
            method: 'GET',
            dataType: 'json',
            success: function(response) {
                updateUnreadCount(response.count);
                updateMessagesList(response.messages);
            },
            error: function() {
                console.log('Error fetching messages');
            }
        });
    }

    /**
     * Update unread message count
     */
    function updateUnreadCount(count) {
        const badge = $('#unreadBadge');
        const countText = $('#unreadCountText');

        if (count > 0) {
            badge.show();
            countText.text('You have ' + count + ' unread message' + (count > 1 ? 's' : ''));
        } else {
            badge.hide();
            countText.text('You have no unread messages');
        }
    }

    /**
     * Update messages list in dropdown
     */
    function updateMessagesList(messages) {
        const messagesList = $('#recentMessagesList');

        if (messages.length === 0) {
            messagesList.html('<div class="p-3 text-center text-muted"><i class="fas fa-inbox fa-2x mb-2"></i><p>No messages yet</p></div>');
            return;
        }

        let html = '';
        messages.forEach(function(message) {
            const avatar = message.sender.profile && message.sender.profile.avatar
                ? message.sender.profile.avatar
                : '/assets/img/faces/6.jpg';

            const messageText = message.content.length > 50
                ? message.content.substring(0, 50) + '...'
                : message.content;

            const timeAgo = formatTimeAgo(message.created_at);

            html += '<a href="/messages/' + message.sender_id + '" class="p-3 d-flex border-bottom">';
            html += '<div class="drop-img cover-image" style="background-image: url(' + avatar + '); width: 40px; height: 40px; background-size: cover; border-radius: 50%; margin-right: 10px;">';
            html += '<span class="avatar-status bg-teal"></span>';
            html += '</div>';
            html += '<div class="wd-90p">';
            html += '<div class="d-flex">';
            html += '<h5 class="mb-1 name">' + message.sender.name + '</h5>';
            html += '</div>';
            html += '<p class="mb-0 desc">' + messageText + '</p>';
            html += '<p class="time mb-0 text-left float-right mr-2 mt-2">' + timeAgo + '</p>';
            html += '</div>';
            html += '</a>';
        });

        messagesList.html(html);
    }

    /**
     * Format time difference
     */
    function formatTimeAgo(timestamp) {
        const messageTime = new Date(timestamp).getTime();
        const now = new Date().getTime();
        const diffMs = now - messageTime;
        const diffMins = Math.floor(diffMs / 60000);
        const diffHours = Math.floor(diffMs / 3600000);
        const diffDays = Math.floor(diffMs / 86400000);

        if (diffMins < 1) return 'Just now';
        if (diffMins < 60) return diffMins + ' min ago';
        if (diffHours < 24) return diffHours + ' hour' + (diffHours > 1 ? 's' : '') + ' ago';
        if (diffDays < 7) return diffDays + ' day' + (diffDays > 1 ? 's' : '') + ' ago';

        return new Date(timestamp).toLocaleDateString();
    }

    /**
     * Auto-scroll message area to bottom when new message is added
     */
    window.scrollChatToBottom = function() {
        const chatBody = document.getElementById('ChatBody');
        if (chatBody) {
            setTimeout(function() {
                chatBody.scrollTop = chatBody.scrollHeight;
            }, 100);
        }
    };
});
