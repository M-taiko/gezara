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
        Schema::table('slaughter_groups', function (Blueprint $table) {
            $table->unsignedBigInteger('updated_by_user_id')->nullable()->after('updated_at');
            $table->text('edit_history')->nullable()->after('notes');
            $table->foreign('updated_by_user_id')->references('id')->on('users')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('slaughter_groups', function (Blueprint $table) {
            $table->dropForeign(['updated_by_user_id']);
            $table->dropColumn(['updated_by_user_id', 'edit_history']);
        });
    }
};
