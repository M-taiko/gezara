<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('slaughter_groups', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->foreignId('animal_id')->constrained('animals')->restrictOnDelete();
            $table->enum('share_type', ['seven', 'five', 'quarter', 'half', 'full']);
            $table->date('slaughter_day')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('slaughter_groups');
    }
};
