<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('contracts', function (Blueprint $table) {
            $table->index('status');
            $table->index('customer_id');
        });

        Schema::table('payments', function (Blueprint $table) {
            $table->index('date');
            $table->index('contract_id');
        });

        Schema::table('animals', function (Blueprint $table) {
            $table->index('status');
        });

        Schema::table('contract_items', function (Blueprint $table) {
            $table->index('animal_id');
            $table->index('contract_id');
        });

        Schema::table('expenses', function (Blueprint $table) {
            $table->index('date');
            $table->index('animal_id');
        });

        Schema::table('slaughter_group_members', function (Blueprint $table) {
            $table->index('group_id');
        });
    }

    public function down(): void
    {
        Schema::table('contracts', function (Blueprint $table) {
            $table->dropIndex(['status']);
            $table->dropIndex(['customer_id']);
        });

        Schema::table('payments', function (Blueprint $table) {
            $table->dropIndex(['date']);
            $table->dropIndex(['contract_id']);
        });

        Schema::table('animals', function (Blueprint $table) {
            $table->dropIndex(['status']);
        });

        Schema::table('contract_items', function (Blueprint $table) {
            $table->dropIndex(['animal_id']);
            $table->dropIndex(['contract_id']);
        });

        Schema::table('expenses', function (Blueprint $table) {
            $table->dropIndex(['date']);
            $table->dropIndex(['animal_id']);
        });

        Schema::table('slaughter_group_members', function (Blueprint $table) {
            $table->dropIndex(['group_id']);
        });
    }
};
