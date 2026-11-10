<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration {
    public function up(): void {
        Schema::create('course_submissions', function (Blueprint $table) {
            $table->id(); $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('starter_pack_id')->constrained()->cascadeOnDelete();
            $table->string('title', 120)->nullable(); $table->text('reflection')->nullable();
            $table->json('photos')->nullable(); $table->string('status', 20)->default('draft');
            $table->timestamp('submitted_at')->nullable(); $table->timestamps();
            $table->unique(['user_id','starter_pack_id']);
        });
    }
    public function down(): void { Schema::dropIfExists('course_submissions'); }
};
