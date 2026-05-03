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
        Schema::table('slaughter_groups', function (Blueprint $table) {
            $table->decimal('min_price', 12, 2)->nullable()->after('animal_type_label');
        });
    }

    public function down(): void
    {
        Schema::table('slaughter_groups', function (Blueprint $table) {
            $table->dropColumn('min_price');
        });
    }
};
