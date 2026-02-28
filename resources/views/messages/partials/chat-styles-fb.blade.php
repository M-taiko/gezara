<style>
    /* ═══════════════════════════════════════════════════════════════════════
       FACEBOOK MESSENGER STYLE LAYOUT
       ═══════════════════════════════════════════════════════════════════════ */

    .messenger-wrapper {
        width: 100%;
        height: calc(100vh - 180px);
        display: flex;
        background: #fff;
        border-radius: 12px;
        overflow: hidden;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
    }

    .messenger-container {
        display: flex;
        width: 100%;
        height: 100%;
        background: #fff;
    }

    /* ─────────────────────────────────────────────────────────────────────
       LEFT SIDEBAR
       ───────────────────────────────────────────────────────────────────── */
    .messenger-sidebar {
        width: 360px;
        background: #fff;
        border-right: 1px solid #e5e7eb;
        display: flex;
        flex-direction: column;
        overflow: hidden;
    }

    .messenger-sidebar-header {
        padding: 16px 20px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        border-bottom: 1px solid #f0f2f5;
    }

    .messenger-title {
        font-size: 32px;
        font-weight: 800;
        color: #111;
        margin: 0;
    }

    .messenger-icons {
        display: flex;
        gap: 8px;
    }

    .messenger-icon-btn {
        width: 36px;
        height: 36px;
        border-radius: 50%;
        background: #f0f2f5;
        border: none;
        color: #0084ff;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: all 0.2s ease;
    }

    .messenger-icon-btn:hover {
        background: #e4e6eb;
    }

    /* Search Bar */
    .messenger-search-container {
        padding: 0 8px 12px 8px;
    }

    .messenger-search-wrapper {
        position: relative;
        display: flex;
        align-items: center;
    }

    .messenger-search-wrapper i {
        position: absolute;
        left: 16px;
        color: #65676b;
        font-size: 14px;
    }

    .messenger-search {
        width: 100%;
        background: #f0f2f5;
        border: none;
        border-radius: 20px;
        padding: 10px 16px 10px 40px;
        font-size: 14px;
        color: #111;
        transition: all 0.2s ease;
    }

    .messenger-search::placeholder {
        color: #b0b3b8;
    }

    .messenger-search:focus {
        outline: none;
        background: #e4e6eb;
    }

    /* Conversations List */
    .messenger-conversations {
        flex: 1;
        overflow-y: auto;
        padding: 0;
    }

    .messenger-conversations::-webkit-scrollbar {
        width: 8px;
    }

    .messenger-conversations::-webkit-scrollbar-track {
        background: transparent;
    }

    .messenger-conversations::-webkit-scrollbar-thumb {
        background: #ccc;
        border-radius: 4px;
    }

    .messenger-conversations::-webkit-scrollbar-thumb:hover {
        background: #999;
    }

    .messenger-conversation-item {
        display: flex;
        gap: 12px;
        padding: 8px 8px;
        margin: 0 8px;
        border-radius: 8px;
        cursor: pointer;
        transition: background 0.15s ease;
        text-decoration: none;
        color: inherit;
        align-items: flex-start;
    }

    .messenger-conversation-item:hover {
        background: #f0f2f5;
    }

    .messenger-conversation-item.active {
        background: #e7f3ff;
    }

    .conversation-avatar-wrapper {
        position: relative;
        flex-shrink: 0;
    }

    .conversation-avatar-img {
        width: 56px;
        height: 56px;
        border-radius: 50%;
        object-fit: cover;
    }

    .online-badge {
        position: absolute;
        width: 14px;
        height: 14px;
        background: #31a24c;
        border: 3px solid white;
        border-radius: 50%;
        bottom: 0;
        right: 0;
        box-shadow: 0 0 0 1px #e5e7eb;
    }

    .conversation-info-wrapper {
        flex: 1;
        min-width: 0;
        padding-top: 2px;
    }

    .conversation-header {
        display: flex;
        justify-content: space-between;
        align-items: baseline;
        gap: 8px;
        margin-bottom: 4px;
    }

    .conversation-name {
        margin: 0;
        font-weight: 500;
        color: #111;
        font-size: 14px;
        text-truncate;
    }

    .conversation-time {
        font-size: 12px;
        color: #65676b;
        flex-shrink: 0;
    }

    .conversation-preview {
        display: flex;
    }

    .preview-text {
        color: #65676b;
        font-size: 13px;
        text-truncate;
    }

    .preview-text.empty {
        color: #bcc0c4;
        font-style: italic;
    }

    .empty-conversations {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        height: 200px;
        color: #bcc0c4;
    }

    .empty-conversations i {
        font-size: 40px;
        margin-bottom: 12px;
    }

    /* ─────────────────────────────────────────────────────────────────────
       RIGHT CHAT AREA
       ───────────────────────────────────────────────────────────────────── */
    .messenger-chat {
        flex: 1;
        display: flex;
        flex-direction: column;
        background: #fff;
        overflow: hidden;
    }

    .chat-empty-state {
        flex: 1;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        color: #bcc0c4;
    }

    .chat-empty-state i {
        font-size: 80px;
        margin-bottom: 20px;
    }

    .chat-empty-state h3 {
        margin: 0 0 8px 0;
        color: #111;
        font-size: 20px;
    }

    .chat-empty-state p {
        margin: 0;
        color: #65676b;
    }

    /* Chat Container */
    .chat-container {
        display: flex;
        flex-direction: column;
        height: 100%;
        background: #fff;
        overflow: hidden;
    }

    /* ─────────────────────────────────────────────────────────────────────
       CHAT HEADER
       ───────────────────────────────────────────────────────────────────── */
    .chat-header {
        background: #fff;
        border-bottom: 1px solid #e5e7eb;
        padding: 16px 20px;
        display: flex;
        align-items: center;
        gap: 12px;
        justify-content: space-between;
    }

    .chat-header-left {
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .chat-header-avatar {
        width: 56px;
        height: 56px;
        border-radius: 50%;
        object-fit: cover;
        border: 2px solid #e5e7eb;
        flex-shrink: 0;
        transition: transform 0.3s ease;
    }

    .chat-header-avatar:hover {
        transform: scale(1.05);
    }

    .chat-header-info h6 {
        margin: 0;
        font-weight: 600;
        color: #111;
        font-size: 15px;
    }

    .chat-header-info small {
        display: block;
        color: #65676b;
        font-size: 12px;
        margin-top: 2px;
    }

    .chat-header-actions {
        display: flex;
        gap: 8px;
    }

    .chat-header-actions button {
        width: 36px;
        height: 36px;
        border-radius: 50%;
        border: none;
        background: #f0f2f5;
        color: #65676b;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: all 0.2s ease;
        font-size: 16px;
    }

    .chat-header-actions button:hover {
        background: #e4e6eb;
        color: #111;
    }

    /* ─────────────────────────────────────────────────────────────────────
       CHAT BODY
       ───────────────────────────────────────────────────────────────────── */
    .chat-body {
        flex: 1;
        overflow-y: auto;
        padding: 16px 20px;
        background-color: #fff;
        display: flex;
        flex-direction: column;
        gap: 8px;
    }

    .chat-body::-webkit-scrollbar {
        width: 8px;
    }

    .chat-body::-webkit-scrollbar-track {
        background: transparent;
    }

    .chat-body::-webkit-scrollbar-thumb {
        background: #ccc;
        border-radius: 4px;
    }

    .chat-body::-webkit-scrollbar-thumb:hover {
        background: #999;
    }

    /* ─────────────────────────────────────────────────────────────────────
       MESSAGE STYLING
       ───────────────────────────────────────────────────────────────────── */
    .message-group {
        display: flex;
        gap: 8px;
        margin-bottom: 4px;
        align-items: flex-end;
        animation: fadeIn 0.3s ease;
    }

    @keyframes fadeIn {
        from {
            opacity: 0;
            transform: translateY(10px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .message-group.sent {
        justify-content: flex-end;
    }

    .message-group.sent .message-content {
        flex-direction: row-reverse;
    }

    .message-group.received {
        justify-content: flex-start;
    }

    .message-content {
        display: flex;
        flex-direction: column;
        align-items: flex-start;
        gap: 4px;
    }

    .message-group.sent .message-content {
        align-items: flex-end;
    }

    .message-avatar {
        width: 32px;
        height: 32px;
        border-radius: 50%;
        object-fit: cover;
        flex-shrink: 0;
    }

    .message-bubble {
        padding: 12px 16px;
        border-radius: 18px;
        max-width: 55%;
        word-wrap: break-word;
        line-height: 1.5;
        font-size: 15px;
        position: relative;
        transition: all 0.2s ease;
    }

    .message-bubble.sent {
        background: linear-gradient(135deg, #0084ff 0%, #0073e6 100%);
        color: white;
        border-bottom-right-radius: 4px;
        box-shadow: 0 1px 2px rgba(0, 84, 255, 0.2);
    }

    .message-bubble.sent:hover {
        box-shadow: 0 2px 6px rgba(0, 84, 255, 0.3);
    }

    .message-bubble.received {
        background-color: #e5e5ea;
        color: #111;
        border-bottom-left-radius: 4px;
        box-shadow: 0 1px 2px rgba(0, 0, 0, 0.06);
    }

    .message-bubble.received:hover {
        background-color: #d9d9df;
    }

    /* Message Meta */
    .message-meta {
        display: flex;
        align-items: center;
        gap: 6px;
        font-size: 12px;
        color: #65676b;
        margin-top: 4px;
        padding: 0 16px;
    }

    .message-group.sent .message-meta {
        justify-content: flex-end;
    }

    .message-time {
        font-size: 12px;
        color: #65676b;
    }

    .message-status {
        font-size: 11px;
        color: #0084ff;
        display: flex;
        align-items: center;
        gap: 3px;
    }

    .message-status .checkmark {
        font-size: 12px;
    }

    .message-status.read {
        color: #0056b3;
    }

    .message-status.unread {
        color: #999;
    }

    /* Edited Label */
    .edited-label {
        font-size: 11px;
        color: rgba(255, 255, 255, 0.8);
        font-style: italic;
        margin-left: 6px;
    }

    .message-bubble.received .edited-label {
        color: #65676b;
    }

    .message-bubble.sent .edited-label {
        color: rgba(255, 255, 255, 0.85);
    }

    /* ─────────────────────────────────────────────────────────────────────
       MESSAGE REACTIONS
       ───────────────────────────────────────────────────────────────────── */
    .message-reactions {
        margin-top: 6px;
        padding: 0 16px;
    }

    .reaction-bubbles {
        display: flex;
        gap: 6px;
        flex-wrap: wrap;
    }

    .reaction-bubble {
        display: inline-flex;
        align-items: center;
        gap: 4px;
        background: #f0f2f5;
        border: 1px solid #e5e7eb;
        border-radius: 16px;
        padding: 4px 12px;
        font-size: 12px;
        cursor: pointer;
        transition: all 0.2s ease;
        user-select: none;
        font-weight: 500;
    }

    .reaction-bubble:hover {
        background: #e4e6eb;
        border-color: #ccc;
        transform: scale(1.08);
    }

    /* ─────────────────────────────────────────────────────────────────────
       MESSAGE ACTIONS
       ───────────────────────────────────────────────────────────────────── */
    .message-actions {
        display: flex;
        gap: 6px;
        padding: 0 16px;
        margin-top: 4px;
        opacity: 0;
        transition: opacity 0.2s ease;
    }

    .message-group:hover .message-actions {
        opacity: 1;
    }

    .message-actions button {
        background: #f0f2f5;
        border: none;
        padding: 6px 12px;
        border-radius: 8px;
        cursor: pointer;
        font-size: 13px;
        color: #0084ff;
        transition: all 0.2s ease;
        display: flex;
        align-items: center;
        gap: 4px;
        font-weight: 500;
    }

    .message-actions button:hover {
        background: #e4e6eb;
        color: #0056b3;
    }

    /* Deleted Message */
    .message-deleted {
        opacity: 0.7;
    }

    .message-deleted .message-bubble {
        opacity: 0.7;
        font-style: italic;
        background: #f5f5f5 !important;
        color: #999 !important;
        border: 1px dashed #ddd !important;
    }

    /* ─────────────────────────────────────────────────────────────────────
       CHAT FOOTER
       ───────────────────────────────────────────────────────────────────── */
    .chat-footer {
        padding: 12px 20px;
        background: #fff;
        border-top: 1px solid #e5e7eb;
        display: flex;
        flex-direction: column;
        gap: 8px;
    }

    .chat-form {
        display: flex;
        gap: 8px;
        align-items: center;
    }

    .chat-form input {
        flex: 1;
        border-radius: 20px;
        border: 2px solid #e5e7eb;
        padding: 12px 18px;
        font-size: 14px;
        transition: all 0.3s ease;
        background-color: #f0f2f5;
        color: #111;
    }

    .chat-form input::placeholder {
        color: #b0b3b8;
    }

    .chat-form input:focus {
        outline: none;
        border-color: #0084ff;
        background-color: #fff;
        box-shadow: 0 0 0 3px rgba(0, 132, 255, 0.1);
    }

    .chat-form button {
        background: linear-gradient(135deg, #0084ff 0%, #0073e6 100%);
        color: white;
        border: none;
        width: 40px;
        height: 40px;
        border-radius: 50%;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: all 0.3s ease;
        box-shadow: 0 2px 8px rgba(0, 84, 255, 0.25);
        font-size: 18px;
    }

    .chat-form button:hover {
        background: linear-gradient(135deg, #0073e6 0%, #0056b3 100%);
        box-shadow: 0 4px 12px rgba(0, 84, 255, 0.35);
        transform: scale(1.08);
    }

    .chat-form button:active {
        transform: scale(0.96);
    }

    .chat-form button:disabled {
        opacity: 0.6;
        cursor: not-allowed;
        box-shadow: 0 2px 8px rgba(0, 84, 255, 0.15);
    }

    /* ─────────────────────────────────────────────────────────────────────
       TYPING INDICATOR
       ───────────────────────────────────────────────────────────────────── */
    .typing-indicator {
        padding: 10px 20px;
        font-size: 13px;
        color: #65676b;
        font-style: italic;
        background: #f8f9fa;
        border-top: 1px solid #e5e7eb;
        display: none;
        animation: slideUp 0.3s ease;
    }

    @keyframes slideUp {
        from {
            opacity: 0;
            transform: translateY(10px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .typing-dots {
        display: inline-flex;
        gap: 4px;
    }

    .typing-dots span {
        display: inline-block;
        width: 8px;
        height: 8px;
        border-radius: 50%;
        background: #0084ff;
        animation: typingBounce 1.4s infinite;
    }

    .typing-dots span:nth-child(2) {
        animation-delay: 0.2s;
    }

    .typing-dots span:nth-child(3) {
        animation-delay: 0.4s;
    }

    @keyframes typingBounce {
        0%, 60%, 100% {
            transform: translateY(0);
            opacity: 1;
        }
        30% {
            transform: translateY(-12px);
            opacity: 0.8;
        }
    }

    /* ─────────────────────────────────────────────────────────────────────
       REACTION PICKER
       ───────────────────────────────────────────────────────────────────── */
    .reaction-picker-inline {
        display: flex;
        gap: 6px;
        padding: 8px;
        background: white;
        border-radius: 12px;
        border: 1px solid #e5e7eb;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.08);
    }

    .reaction-btn {
        background: transparent;
        border: 1px solid #e5e7eb;
        padding: 6px 8px;
        border-radius: 8px;
        cursor: pointer;
        font-size: 20px;
        transition: all 0.2s ease;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .reaction-btn:hover {
        background: #f0f2f5;
        transform: scale(1.25);
        border-color: #0084ff;
    }

    /* ─────────────────────────────────────────────────────────────────────
       DARK MODE
       ───────────────────────────────────────────────────────────────────── */

    .dark-mode .messenger-wrapper {
        background: #1e1e1e;
    }

    .dark-mode .messenger-container {
        background: #1e1e1e;
    }

    .dark-mode .messenger-sidebar {
        background: #1e1e1e;
        border-right-color: #333;
    }

    .dark-mode .messenger-sidebar-header {
        border-bottom-color: #333;
    }

    .dark-mode .messenger-title {
        color: #fff;
    }

    .dark-mode .messenger-icon-btn {
        background: #333;
        color: #64b5f6;
    }

    .dark-mode .messenger-icon-btn:hover {
        background: #404040;
    }

    .dark-mode .messenger-search {
        background: #333;
        color: #fff;
    }

    .dark-mode .messenger-search::placeholder {
        color: #666;
    }

    .dark-mode .messenger-search:focus {
        background: #404040;
    }

    .dark-mode .messenger-search-wrapper i {
        color: #999;
    }

    .dark-mode .messenger-conversation-item:hover {
        background: #2a2a2a;
    }

    .dark-mode .messenger-conversation-item.active {
        background: rgba(0, 132, 255, 0.15);
    }

    .dark-mode .conversation-name {
        color: #fff;
    }

    .dark-mode .conversation-time {
        color: #999;
    }

    .dark-mode .conversation-preview {
        color: #aaa;
    }

    .dark-mode .empty-conversations {
        color: #666;
    }

    .dark-mode .chat-empty-state {
        color: #666;
    }

    .dark-mode .chat-empty-state i {
        color: #333;
    }

    .dark-mode .chat-empty-state h3 {
        color: #fff;
    }

    .dark-mode .chat-empty-state p {
        color: #aaa;
    }

    .dark-mode .chat-container {
        background: #1e1e1e;
    }

    .dark-mode .chat-header {
        background: #1e1e1e;
        border-bottom-color: #333;
    }

    .dark-mode .chat-header-info h6 {
        color: #fff;
    }

    .dark-mode .chat-header-info small {
        color: #aaa;
    }

    .dark-mode .chat-header-actions button {
        background: #333;
        color: #aaa;
    }

    .dark-mode .chat-header-actions button:hover {
        background: #404040;
        color: #fff;
    }

    .dark-mode .chat-body {
        background-color: #1e1e1e;
    }

    .dark-mode .message-bubble.received {
        background-color: #333;
        color: #fff;
    }

    .dark-mode .message-bubble.received:hover {
        background-color: #3a3a3a;
    }

    .dark-mode .message-time {
        color: #999;
    }

    .dark-mode .message-status {
        color: #64b5f6;
    }

    .dark-mode .chat-form input {
        background: #333;
        color: #fff;
        border-color: #444;
    }

    .dark-mode .chat-form input::placeholder {
        color: #666;
    }

    .dark-mode .chat-form input:focus {
        background: #404040;
        border-color: #0084ff;
    }

    .dark-mode .chat-footer {
        background: #1e1e1e;
        border-top-color: #333;
    }

    .dark-mode .reaction-bubble {
        background: #333;
        border-color: #444;
        color: #fff;
    }

    .dark-mode .reaction-bubble:hover {
        background: #404040;
    }

    .dark-mode .message-actions button {
        background: #333;
        color: #64b5f6;
    }

    .dark-mode .message-actions button:hover {
        background: #404040;
        color: #42a5f5;
    }

    .dark-mode .typing-indicator {
        background: #2a2a2a;
        color: #999;
        border-top-color: #333;
    }

    .dark-mode .reaction-picker-inline {
        background: #2a2a2a;
        border-color: #333;
    }

    .dark-mode .reaction-btn {
        border-color: #444;
    }

    .dark-mode .reaction-btn:hover {
        background: #333;
    }

    /* ─────────────────────────────────────────────────────────────────────
       RESPONSIVE DESIGN
       ───────────────────────────────────────────────────────────────────── */

    @media (max-width: 1024px) {
        .messenger-sidebar {
            width: 300px;
        }

        .messenger-title {
            font-size: 28px;
        }
    }

    @media (max-width: 768px) {
        .messenger-wrapper {
            flex-direction: column;
        }

        .messenger-sidebar {
            width: 100%;
            height: 40%;
            border-right: none;
            border-bottom: 1px solid #e5e7eb;
        }

        .messenger-chat {
            height: 60%;
        }

        .message-bubble {
            max-width: 80%;
        }
    }

    @media (max-width: 480px) {
        .messenger-wrapper {
            height: calc(100vh - 120px);
        }

        .messenger-sidebar {
            height: 35%;
        }

        .messenger-chat {
            height: 65%;
        }

        .messenger-title {
            font-size: 24px;
        }

        .message-bubble {
            max-width: 90%;
        }
    }
</style>
