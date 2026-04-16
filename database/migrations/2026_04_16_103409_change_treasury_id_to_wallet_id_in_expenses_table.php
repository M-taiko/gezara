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
        Schema::table('expenses', function (Blueprint $table) {
            // Drop the old foreign key
            $table->dropForeign(['treasury_id']);
            // Rename the column
            $table->renameColumn('treasury_id', 'wallet_id');
            // Add the new foreign key
            $table->foreign('wallet_id')->references('id')->on('wallets')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('expenses', function (Blueprint $table) {
            // Drop the new foreign key
            $table->dropForeign(['wallet_id']);
            // Rename back
            $table->renameColumn('wallet_id', 'treasury_id');
            // Add back the old foreign key
            $table->foreign('treasury_id')->references('id')->on('treasuries')->onDelete('restrict');
        });
    }
};
