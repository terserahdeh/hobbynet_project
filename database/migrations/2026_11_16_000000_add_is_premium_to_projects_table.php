<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('projects', 'is_premium')) {
            Schema::table('projects', function (Blueprint $table) {
                // true = isi langkah hanya untuk HobbyNet Plus; pengguna gratis hanya bisa melihat pratinjau.
                $table->boolean('is_premium')->default(false);
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('projects', 'is_premium')) {
            Schema::table('projects', fn (Blueprint $t) => $t->dropColumn('is_premium'));
        }
    }
};
