<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('slaughter_group_members', function (Blueprint $table) {
            $table->id();
            $table->foreignId('group_id')->constrained('slaughter_groups')->cascadeOnDelete();
            $table->foreignId('customer_id')->constrained('customers')->restrictOnDelete();
            $table->foreignId('contract_item_id')->nullable()->constrained('contract_items')->nullOnDelete();
            $table->unsignedTinyInteger('shares_count')->default(1);
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->unique(['group_id', 'customer_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('slaughter_group_members');
    }
};
