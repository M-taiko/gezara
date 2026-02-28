<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('messages', function (Blueprint $table) {
            $table->softDeletes(); // deleted_at column
            $table->timestamp('edited_at')->nullable();
            $table->text('original_content')->nullable();
            $table->foreignId('conversation_id')->nullable()->constrained()->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('messages', function (Blueprint $table) {
            $table->dropSoftDeletes();
            $table->dropColumn(['edited_at', 'original_content', 'conversation_id']);
        });
    }
};
