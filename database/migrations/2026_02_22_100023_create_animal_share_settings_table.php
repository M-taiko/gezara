<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration {
    public function up(): void {
        Schema::create('animal_share_settings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('animal_id')->unique()->constrained()->cascadeOnDelete();
            $table->enum('share_type', ['seven','five','quarter','half']);
            $table->unsignedTinyInteger('total_shares');
            $table->unsignedTinyInteger('sold_shares')->default(0);
            $table->unsignedTinyInteger('remaining_shares');
            $table->timestamps();
        });
    }
    public function down(): void { Schema::dropIfExists('animal_share_settings'); }
};