<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('roadmap_steps', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained()->cascadeOnDelete();
            $table->unsignedTinyInteger('position');
            $table->string('slug');
            $table->string('title');
            $table->string('summary');
            $table->string('objective')->nullable();
            // materials | lesson | practice | project | share
            $table->string('type', 20)->default('lesson');
            $table->unsignedSmallInteger('estimated_minutes')->default(15);
            $table->text('encouragement')->nullable();
            $table->string('icon', 30)->default('default');
            $table->foreignId('badge_id')->nullable()->constrained()->nullOnDelete();
            $table->timestamps();

            $table->unique(['project_id', 'position']);
            $table->unique(['project_id', 'slug']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('roadmap_steps');
    }
};
