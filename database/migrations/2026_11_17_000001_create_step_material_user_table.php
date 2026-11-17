<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /** Which materials a learner says they already own ("Sudah punya"). */
    public function up(): void
    {
        Schema::create('step_material_user', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('step_material_id')->constrained()->cascadeOnDelete();
            $table->timestamps();

            $table->unique(['user_id', 'step_material_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('step_material_user');
    }
};
