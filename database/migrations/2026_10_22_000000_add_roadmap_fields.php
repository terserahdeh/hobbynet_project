<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('projects', function (Blueprint $table) {
            $table->string('skill')->nullable()->after('summary'); // teknik utama proyek
        });

        Schema::table('user_projects', function (Blueprint $table) {
            $table->unsignedTinyInteger('steps_done')->default(0)->after('project_id'); // 0..6
            $table->boolean('is_roadmap')->default(false)->after('steps_done');         // roadmap utama di beranda
            $table->timestamp('completed_at')->nullable()->after('last_worked_at');
        });

        Schema::table('users', function (Blueprint $table) {
            $table->timestamp('default_roadmap_created_at')->nullable()->after('daily_goal_minutes');
        });

        // Selaraskan data lama: persen progres menjadi jumlah langkah (dari 6).
        DB::table('user_projects')->orderBy('id')->each(function ($row) {
            DB::table('user_projects')->where('id', $row->id)->update([
                'steps_done' => (int) floor($row->progress_percent * 6 / 100),
            ]);
        });
    }

    public function down(): void
    {
        Schema::table('users', fn (Blueprint $t) => $t->dropColumn('default_roadmap_created_at'));
        Schema::table('user_projects', fn (Blueprint $t) => $t->dropColumn(['steps_done', 'is_roadmap', 'completed_at']));
        Schema::table('projects', fn (Blueprint $t) => $t->dropColumn('skill'));
    }
};
