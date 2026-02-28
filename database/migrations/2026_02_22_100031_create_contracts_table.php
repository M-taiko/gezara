<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration {
    public function up(): void {
        Schema::create('contracts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('customer_id')->constrained()->restrictOnDelete();
            $table->string('contract_number')->unique();
            $table->date('slaughter_day')->nullable();
            $table->unsignedTinyInteger('slaughter_order')->nullable();
            $table->text('notes')->nullable();
            $table->decimal('total_amount', 12, 2)->default(0);
            $table->decimal('paid_amount', 12, 2)->default(0);
            $table->decimal('remaining_amount', 12, 2)->default(0);
            $table->enum('status', ['active','completed','cancelled'])->default('active');
            $table->timestamps();
        });
    }
    public function down(): void { Schema::dropIfExists('contracts'); }
};