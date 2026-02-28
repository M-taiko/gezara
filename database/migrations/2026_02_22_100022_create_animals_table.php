<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration {
    public function up(): void {
        Schema::create('animals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->restrictOnDelete();
            $table->foreignId('supplier_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('purchase_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('warehouse_id')->constrained()->restrictOnDelete();
            $table->string('code')->unique();
            $table->decimal('weight', 8, 2)->nullable();
            $table->decimal('cost', 12, 2)->default(0);
            $table->boolean('is_grouped')->default(false);
            $table->enum('status', ['available','partially_allocated','fully_allocated','slaughtered'])->default('available');
            $table->decimal('price_full', 12, 2)->nullable();
            $table->decimal('price_seven', 12, 2)->nullable();
            $table->decimal('price_five', 12, 2)->nullable();
            $table->decimal('price_quarter', 12, 2)->nullable();
            $table->decimal('price_half', 12, 2)->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }
    public function down(): void { Schema::dropIfExists('animals'); }
};