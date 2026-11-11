<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Who can open /admin.
        Schema::table('users', function (Blueprint $table) {
            $table->boolean('is_admin')->default(false)->after('password');
        });

        // Learners only see a roadmap once an admin has published it.
        Schema::table('projects', function (Blueprint $table) {
            $table->boolean('roadmap_published')->default(false)->after('is_popular');
        });

        // The demonstration picture shown beside a step's instructions.
        Schema::table('roadmap_steps', function (Blueprint $table) {
            $table->string('image')->nullable()->after('icon');
            $table->string('image_caption')->nullable()->after('image');
        });

        // Roadmaps that already exist keep working for learners.
        DB::table('projects')
            ->whereIn('id', DB::table('roadmap_steps')->select('project_id'))
            ->update(['roadmap_published' => true]);
    }

    public function down(): void
    {
        Schema::table('roadmap_steps', function (Blueprint $table) {
            $table->dropColumn(['image', 'image_caption']);
        });
        Schema::table('projects', function (Blueprint $table) {
            $table->dropColumn('roadmap_published');
        });
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('is_admin');
        });
    }
};
