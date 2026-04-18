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
            $table->string('animal_type_label')->nullable()->after('animal_id')->comment('مثال: عجل بقري، عجل جاموسي');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('slaughter_groups', function (Blueprint $table) {
            $table->dropColumn('animal_type_label');
        });
    }
};
