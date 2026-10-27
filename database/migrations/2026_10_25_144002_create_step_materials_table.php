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
        Schema::create('step_materials', function (Blueprint $table) {
            $table->id();
            $table->foreignId('roadmap_step_id')->constrained()->cascadeOnDelete();
            $table->unsignedTinyInteger('position')->default(0);
            // tool | material
            $table->string('kind', 20)->default('tool');
            $table->string('name');
            $table->string('spec')->nullable();
            $table->boolean('is_optional')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('step_materials');
    }
};
