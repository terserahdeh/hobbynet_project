<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('projects', 'skill')) {
            Schema::table('projects', function (Blueprint $table) {
                // Teknik utama proyek, mis. "tusuk tunggal dan lingkaran ajaib"
                $table->string('skill')->nullable()->after('summary');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('projects', 'skill')) {
            Schema::table('projects', fn (Blueprint $t) => $t->dropColumn('skill'));
        }
    }
};