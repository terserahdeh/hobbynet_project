<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // "Pro Tip" di halaman langkah
        Schema::table('roadmap_steps', function (Blueprint $table) {
            $table->text('tip')->nullable()->after('encouragement');
        });

        // "What You'll Learn" di halaman detail proyek: [[judul, deskripsi], ...]
        Schema::table('projects', function (Blueprint $table) {
            $table->json('learnings')->nullable();
        });

        // Centang bahan yang sudah dimiliki pengguna ("0 of 4 ready")
        Schema::create('project_material_ready', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('project_id')->constrained()->cascadeOnDelete();
            $table->string('name', 150);
            $table->timestamps();

            $table->unique(['user_id', 'project_id', 'name']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('project_material_ready');
        Schema::table('projects', fn (Blueprint $t) => $t->dropColumn('learnings'));
        Schema::table('roadmap_steps', fn (Blueprint $t) => $t->dropColumn('tip'));
    }
};
