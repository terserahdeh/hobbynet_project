<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Video YouTube per langkah (disimpan sebagai ID 11 karakter). Dipakai roadmap dan proyek berpemandu.
        if (! Schema::hasColumn('roadmap_steps', 'video_url')) {
            Schema::table('roadmap_steps', function (Blueprint $table) {
                $table->string('video_url', 255)->nullable();
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('roadmap_steps', 'video_url')) {
            Schema::table('roadmap_steps', fn (Blueprint $t) => $t->dropColumn('video_url'));
        }
    }
};