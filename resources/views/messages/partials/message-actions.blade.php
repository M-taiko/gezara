<!-- Message Actions (React, Edit, Delete) -->
<div class="message-actions" data-message-actions="{{ $message->id }}">
    <button class="btn-react" data-message-id="{{ $message->id }}" title="React with emoji">
        <i class="far fa-smile"></i>
    </button>
    <button class="btn-edit-message" data-message-id="{{ $message->id }}" title="Edit message">
        <i class="fas fa-edit"></i>
    </button>
    <button class="btn-delete-message" data-message-id="{{ $message->id }}" title="Delete message">
        <i class="fas fa-trash"></i>
    </button>
</div>
