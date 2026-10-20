<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('experience_level', 20)->nullable()->after('onboarding_step'); // beginner | some | returning
            $table->json('tools')->nullable()->after('experience_level');
            $table->string('budget', 20)->nullable()->after('tools');                     // budget | standard | premium
            $table->unsignedSmallInteger('daily_goal_minutes')->default(45)->after('budget');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['experience_level', 'tools', 'budget', 'daily_goal_minutes']);
        });
    }
};
