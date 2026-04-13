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
        Schema::table('purchase_items', function (Blueprint $table) {
            $table->decimal('price_full', 12, 2)->nullable()->after('total');
            $table->decimal('price_half', 12, 2)->nullable()->after('price_full');
            $table->decimal('price_third', 12, 2)->nullable()->after('price_half');
            $table->decimal('price_quarter', 12, 2)->nullable()->after('price_third');
            $table->decimal('price_five', 12, 2)->nullable()->after('price_quarter');
            $table->decimal('price_six', 12, 2)->nullable()->after('price_five');
            $table->decimal('price_seven', 12, 2)->nullable()->after('price_six');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('purchase_items', function (Blueprint $table) {
            $table->dropColumn([
                'price_full', 'price_half', 'price_third', 'price_quarter',
                'price_five', 'price_six', 'price_seven'
            ]);
        });
    }
};
