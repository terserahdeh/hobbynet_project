<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("
            ALTER TABLE step_materials
            ADD COLUMN item_url VARCHAR(2048) NULL,
            ADD COLUMN description TEXT NULL,
            ADD COLUMN price DECIMAL(12,2) NULL,
            ADD COLUMN image LONGBLOB NULL
        ");
    }

    public function down(): void
    {
        DB::statement("
            ALTER TABLE step_materials
            DROP COLUMN item_url,
            DROP COLUMN description,
            DROP COLUMN price,
            DROP COLUMN image
        ");
    }
};