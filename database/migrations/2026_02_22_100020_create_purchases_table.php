<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration {
    public function up(): void {
        Schema::create('purchases', function (Blueprint $table) {
            $table->id();
            $table->foreignId('supplier_id')->constrained()->restrictOnDelete();
            $table->date('date');
            $table->text('notes')->nullable();
            $table->decimal('total', 12, 2)->default(0);
            $table->decimal('paid', 12, 2)->default(0);
            $table->enum('status', ['draft', 'confirmed'])->default('confirmed');
            $table->timestamps();
        });
    }
    public function down(): void { Schema::dropIfExists('purchases'); }
};