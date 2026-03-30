<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("ALTER TABLE slaughter_groups MODIFY COLUMN share_type ENUM('seven','six','five','quarter','third','half','full') NOT NULL");
        DB::statement("ALTER TABLE contract_items MODIFY COLUMN share_type ENUM('seven','six','five','quarter','third','half','full') NULL");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE slaughter_groups MODIFY COLUMN share_type ENUM('seven','five','quarter','half','full') NOT NULL");
        DB::statement("ALTER TABLE contract_items MODIFY COLUMN share_type ENUM('seven','five','quarter','half','full') NULL");
    }
};
