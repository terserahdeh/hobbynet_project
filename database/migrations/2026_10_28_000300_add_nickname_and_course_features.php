<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::table('users', function (Blueprint $table) { $table->string('nickname', 40)->nullable(); });
        Schema::table('starter_packs', function (Blueprint $table) { $table->boolean('is_premium')->default(false); });
        Schema::create('course_progress', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('starter_pack_id')->constrained()->cascadeOnDelete();
            $table->unsignedSmallInteger('lesson_number');
            $table->timestamps();
            $table->unique(['user_id', 'starter_pack_id', 'lesson_number']);
        });
        DB::table('starter_packs')->whereIn('slug', ['cerita-foto-ponsel', 'kartu-pos-cat-air'])->update(['is_premium' => true]);
    }
    public function down(): void {
        Schema::dropIfExists('course_progress');
        Schema::table('starter_packs', fn (Blueprint $table) => $table->dropColumn('is_premium'));
        Schema::table('users', fn (Blueprint $table) => $table->dropColumn('nickname'));
    }
};
