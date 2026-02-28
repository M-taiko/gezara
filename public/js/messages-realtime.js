/**
 * Real-time messaging with Laravel Echo and Reverb
 * Handles WebSocket events, typing indicators, reactions, and message updates
 */
(function() {
    'use strict';

    // Configuration
    const CONFIG = {
        typingTimeout: 3000, // Stop typing after 3 seconds of inactivity
        typingDebounce: 300, // Debounce typing events
    };

    // State
    let typingTimer = null;
    let isCurrentlyTyping = false;

    // DOM Elements
    const chatBody = document.getElementById('ChatBody');
    const messageInput = document.getElementById('messageInput');
    const messageForm = document.getElementById('messageForm');
    const typingIndicator = document.getElementById('typingIndicator');

    // Get data attributes
    const conversationId = messageForm?.dataset.conversationId;
    const currentUserId = messageForm?.dataset.currentUserId;
    const otherUserId = messageForm?.dataset.otherUserId;

    /**
     * Initialize real-time listeners
     */
    function initializeRealTime() {
        if (!window.Echo || !conversationId) {
            console.warn('Echo not available or missing conversation data');
            return;
        }

        // Subscribe to conversation channel
        const conversationChannel = window.Echo.private(`conversation.${conversationId}`);

        // Listen for new messages
        conversationChannel.listen('.message.sent', (event) => {
            console.log('Message received:', event);
            appendMessage(event.message);
            scrollToBottom();

            // Mark as read if window is focused
            if (document.hasFocus() && event.message.receiver_id == currentUserId) {
                markMessageAsRead(event.message.id);
            }
        });

        // Listen for message edits
        conversationChannel.listen('.message.edited', (event) => {
            console.log('Message edited:', event);
            updateMessage(event.message);
        });

        // Listen for message deletions
        conversationChannel.listen('.message.deleted', (event) => {
            console.log('Message deleted:', event);
            removeMessage(event.message_id);
        });

        // Listen for reactions added
        conversationChannel.listen('.message.reaction.added', (event) => {
            console.log('Reaction added:', event);
            updateMessageReactions(event.reaction.message_id);
        });

        // Listen for reactions removed
        conversationChannel.listen('.message.reaction.removed', (event) => {
            console.log('Reaction removed:', event);
            updateMessageReactions(event.message_id);
        });

        // Listen for read receipts
        conversationChannel.listen('.message.read', (event) => {
            console.log('Message read:', event);
            markMessageAsReadInUI(event.message_id);
        });

        // Subscribe to user channel for typing indicators
        const userChannel = window.Echo.private(`user.${currentUserId}`);

        userChannel.listen('.typing.started', (event) => {
            console.log('User started typing:', event);
            if (event.conversation_id == conversationId) {
                showTypingIndicator(event.user_name);
            }
        });

        userChannel.listen('.typing.stopped', (event) => {
            console.log('User stopped typing:', event);
            if (event.conversation_id == conversationId) {
                hideTypingIndicator();
            }
        });

        console.log('Real-time messaging initialized');
    }

    /**
     * Append new message to chat
     */
    function appendMessage(message) {
        // Check if message already exists
        if (document.querySelector(`[data-message-id="${message.id}"]`)) {
            return;
        }

        const isSent = message.sender_id == currentUserId;
        const avatar = message.sender?.avatar || '/assets/img/faces/6.jpg';

        const messageHtml = `
            <div class="message-group ${isSent ? 'sent' : 'received'}" data-message-id="${message.id}">
                ${!isSent ? `<img src="${avatar}" alt="avatar" class="message-avatar">` : ''}
                <div class="${isSent ? 'text-right' : ''}">
                    <div class="message-bubble ${isSent ? 'sent' : 'received'}" data-message-content="${message.id}">
                        ${escapeHtml(message.content)}
                    </div>
                    <div class="message-meta">
                        <span class="message-time">${formatTime(message.created_at)}</span>
                        ${isSent ? `<span class="message-status ${message.is_read ? 'read' : 'unread'}" data-message-status="${message.id}">
                            <i class="fas fa-check${message.is_read ? '-double' : ''} checkmark"></i>
                            ${message.is_read ? 'Read' : 'Sent'}
                        </span>` : ''}
                    </div>
                    <div class="message-reactions" data-message-reactions="${message.id}"></div>
                    ${isSent ? `
                    <div class="message-actions" data-message-actions="${message.id}">
                        <button class="btn-react" data-message-id="${message.id}" title="React">
                            <i class="far fa-smile"></i>
                        </button>
                        <button class="btn-edit-message" data-message-id="${message.id}" title="Edit">
                            <i class="fas fa-edit"></i>
                        </button>
                        <button class="btn-delete-message" data-message-id="${message.id}" title="Delete">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>` : ''}
                </div>
            </div>
        `;

        chatBody.insertAdjacentHTML('beforeend', messageHtml);
        attachMessageEventHandlers(message.id);
    }

    /**
     * Update message content after edit
     */
    function updateMessage(message) {
        const messageElement = document.querySelector(`[data-message-content="${message.id}"]`);
        if (messageElement) {
            messageElement.innerHTML = escapeHtml(message.content);

            // Add edited indicator
            if (!messageElement.querySelector('.edited-label')) {
                messageElement.insertAdjacentHTML('afterend', ' <span class="edited-label">(edited)</span>');
            }
        }
    }

    /**
     * Remove message from UI (soft delete)
     */
    function removeMessage(messageId) {
        const messageGroup = document.querySelector(`[data-message-id="${messageId}"]`);
        if (messageGroup) {
            messageGroup.classList.add('message-deleted');
            const bubble = messageGroup.querySelector('.message-bubble');
            if (bubble) {
                bubble.innerHTML = '<em>This message was deleted</em>';
                bubble.classList.add('deleted');
            }
        }
    }

    /**
     * Typing indicator handlers
     */
    function handleTyping() {
        // Debounce typing events
        clearTimeout(typingTimer);

        if (!isCurrentlyTyping) {
            sendTypingStarted();
            isCurrentlyTyping = true;
        }

        // Auto-stop typing after timeout
        typingTimer = setTimeout(() => {
            sendTypingStopped();
            isCurrentlyTyping = false;
        }, CONFIG.typingTimeout);
    }

    function sendTypingStarted() {
        window.axios.post('/api/typing/start', {
            conversation_id: conversationId,
            recipient_id: otherUserId,
        }).catch(err => console.error('Typing start error:', err));
    }

    function sendTypingStopped() {
        window.axios.post('/api/typing/stop', {
            conversation_id: conversationId,
            recipient_id: otherUserId,
        }).catch(err => console.error('Typing stop error:', err));
    }

    function showTypingIndicator(userName) {
        if (typingIndicator) {
            typingIndicator.textContent = `${userName} is typing...`;
            typingIndicator.style.display = 'block';
            scrollToBottom();
        }
    }

    function hideTypingIndicator() {
        if (typingIndicator) {
            typingIndicator.style.display = 'none';
        }
    }

    /**
     * Message reactions
     */
    function addReaction(messageId, reactionType) {
        window.axios.post(`/messages/${messageId}/reactions`, {
            reaction_type: reactionType,
        })
        .then(response => {
            console.log('Reaction added:', response.data);
            updateMessageReactions(messageId);
        })
        .catch(err => console.error('Reaction error:', err));
    }

    function updateMessageReactions(messageId) {
        // Fetch latest reactions for message
        window.axios.get(`/api/messages/${messageId}/reactions`)
            .then(response => {
                const reactionsContainer = document.querySelector(`[data-message-reactions="${messageId}"]`);
                if (reactionsContainer && response.data.reactions) {
                    reactionsContainer.innerHTML = renderReactions(response.data.reactions);
                }
            })
            .catch(err => {
                // If endpoint doesn't exist, refresh reactions from visible messages
                console.log('Reactions endpoint not found, skipping update');
            });
    }

    function renderReactions(reactions) {
        if (!reactions || Object.keys(reactions).length === 0) return '';

        const reactionIcons = {
            like: '👍',
            love: '❤️',
            haha: '😄',
            wow: '😮',
            sad: '😢',
            angry: '😠'
        };

        let html = '<div class="reaction-bubbles">';
        Object.entries(reactions).forEach(([type, count]) => {
            html += `<span class="reaction-bubble" data-reaction="${type}" onclick="MessagingRealTime.toggleReaction('${type}')">
                ${reactionIcons[type]} ${count}
            </span>`;
        });
        html += '</div>';

        return html;
    }

    /**
     * Mark message as read
     */
    function markMessageAsRead(messageId) {
        window.axios.post(`/messages/${messageId}/read`)
            .catch(err => console.error('Mark read error:', err));
    }

    function markMessageAsReadInUI(messageId) {
        const statusElement = document.querySelector(`[data-message-status="${messageId}"]`);
        if (statusElement) {
            statusElement.classList.remove('unread');
            statusElement.classList.add('read');
            statusElement.innerHTML = '<i class="fas fa-check-double checkmark"></i> Read';
        }
    }

    /**
     * Utility functions
     */
    function scrollToBottom() {
        if (chatBody) {
            setTimeout(() => {
                chatBody.scrollTop = chatBody.scrollHeight;
            }, 0);
        }
    }

    function escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }

    function formatTime(timestamp) {
        const date = new Date(timestamp);
        return date.toLocaleTimeString('en-US', { hour: '2-digit', minute: '2-digit', hour12: true });
    }

    function attachMessageEventHandlers(messageId) {
        // React button
        const reactBtn = document.querySelector(`[data-message-actions="${messageId}"] .btn-react`);
        if (reactBtn) {
            reactBtn.addEventListener('click', () => handleShowReactionPicker(messageId));
        }

        // Edit button
        const editBtn = document.querySelector(`[data-message-actions="${messageId}"] .btn-edit-message`);
        if (editBtn) {
            editBtn.addEventListener('click', () => handleEditMessage(messageId));
        }

        // Delete button
        const deleteBtn = document.querySelector(`[data-message-actions="${messageId}"] .btn-delete-message`);
        if (deleteBtn) {
            deleteBtn.addEventListener('click', () => handleDeleteMessage(messageId));
        }
    }

    function handleShowReactionPicker(messageId) {
        const reactions = ['like', 'love', 'haha', 'wow', 'sad', 'angry'];
        const reactionIcons = {
            like: '👍',
            love: '❤️',
            haha: '😄',
            wow: '😮',
            sad: '😢',
            angry: '😠'
        };

        // Create inline reaction picker
        const pickerHtml = reactions
            .map(type => `<button class="reaction-btn" data-reaction="${type}" onclick="MessagingRealTime.addReaction(${messageId}, '${type}')">${reactionIcons[type]}</button>`)
            .join('');

        const messageElement = document.querySelector(`[data-message-id="${messageId}"]`);
        if (messageElement && !messageElement.querySelector('.reaction-picker-inline')) {
            const picker = document.createElement('div');
            picker.className = 'reaction-picker-inline';
            picker.innerHTML = pickerHtml;
            messageElement.querySelector('.message-bubble').appendChild(picker);
        }
    }

    function handleEditMessage(messageId) {
        const messageContent = document.querySelector(`[data-message-content="${messageId}"]`);
        if (!messageContent) return;

        const currentText = messageContent.textContent.replace(' (edited)', '').trim();
        const newText = prompt('Edit message:', currentText);

        if (newText && newText !== currentText) {
            window.axios.put(`/messages/${messageId}`, { content: newText })
                .then(() => console.log('Message edited'))
                .catch(err => {
                    alert('Error editing message');
                    console.error(err);
                });
        }
    }

    function handleDeleteMessage(messageId) {
        if (!confirm('Delete this message?')) return;

        window.axios.delete(`/messages/${messageId}`)
            .then(() => console.log('Message deleted'))
            .catch(err => {
                alert('Error deleting message');
                console.error(err);
            });
    }

    /**
     * Initialize on DOM ready
     */
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initializeRealTime);
    } else {
        initializeRealTime();
    }

    // Attach typing handler
    if (messageInput) {
        messageInput.addEventListener('input', handleTyping);

        // Stop typing on blur
        messageInput.addEventListener('blur', () => {
            if (isCurrentlyTyping) {
                sendTypingStopped();
                isCurrentlyTyping = false;
            }
            clearTimeout(typingTimer);
        });

        // Stop typing on submit
        if (messageForm) {
            messageForm.addEventListener('submit', () => {
                if (isCurrentlyTyping) {
                    sendTypingStopped();
                    isCurrentlyTyping = false;
                }
                clearTimeout(typingTimer);
            });
        }
    }

    // Expose functions globally for use
    window.MessagingRealTime = {
        addReaction,
        scrollToBottom,
        toggleReaction: addReaction,
    };

})();
