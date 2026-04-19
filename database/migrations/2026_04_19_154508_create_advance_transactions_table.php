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
        Schema::create('advance_transactions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('advance_id');
            $table->enum('type', ['receipt', 'return'])->comment('استلام أم رد');
            $table->unsignedBigInteger('wallet_id')->nullable();
            $table->decimal('amount', 15, 2);
            $table->text('notes')->nullable();
            $table->timestamp('date');
            $table->timestamps();

            $table->foreign('advance_id')->references('id')->on('advances')->cascadeOnDelete();
            $table->foreign('wallet_id')->references('id')->on('wallets')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('advance_transactions');
    }
};
