<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('projects', function (Blueprint $table) {
            $table->id();
            $table->foreignId('hobby_id')->nullable()->constrained()->nullOnDelete();
            $table->string('slug')->unique();
            $table->string('title');
            $table->string('category');                    // label tampilan, mis. "Merajut", "Memanggang"
            $table->string('summary');
            $table->text('description')->nullable();
            $table->string('level', 20)->default('beginner');
            $table->unsignedInteger('duration_minutes')->default(30);
            $table->unsignedInteger('learners_count')->default(0);
            $table->string('image')->nullable();           // path relatif di /public
            $table->string('icon', 30)->default('default');
            $table->string('color', 10)->default('pink');  // pink | sage | peri
            $table->boolean('is_starter')->default(false); // proyek pertama untuk sebuah hobi
            $table->boolean('is_popular')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('projects');
    }
};
