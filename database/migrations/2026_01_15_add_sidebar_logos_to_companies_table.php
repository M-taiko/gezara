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
        Schema::table('companies', function (Blueprint $table) {
            $table->string('sidebar_logo_expanded')->nullable()->after('logo');
            $table->string('sidebar_logo_collapsed')->nullable()->after('sidebar_logo_expanded');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('companies', function (Blueprint $table) {
            $table->dropColumn('sidebar_logo_expanded');
            $table->dropColumn('sidebar_logo_collapsed');
        });
    }
};
