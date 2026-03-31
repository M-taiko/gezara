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
        Schema::table('meat_inventory', function (Blueprint $table) {
            $table->decimal('sold_weight_kg', 8, 2)->default(0)->after('weight_kg');
        });
    }

    public function down(): void
    {
        Schema::table('meat_inventory', function (Blueprint $table) {
            $table->dropColumn('sold_weight_kg');
        });
    }
};
