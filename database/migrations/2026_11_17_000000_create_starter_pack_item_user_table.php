<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // "Aku sudah punya" per item perlengkapan, dipakai Jelajahi, proyek, dan roadmap.
        Schema::create('starter_pack_item_user', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('starter_pack_item_id')->constrained()->cascadeOnDelete();
            $table->timestamps();

            $table->unique(['user_id', 'starter_pack_item_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('starter_pack_item_user');
    }
};