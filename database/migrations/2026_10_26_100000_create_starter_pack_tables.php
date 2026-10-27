<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Satu starter pack menempel pada satu Project yang sudah ada.
        Schema::create('starter_packs', function (Blueprint $table) {
            $table->id();
            $table->string('slug')->unique();
            $table->foreignId('project_id')->unique()->constrained('projects')->cascadeOnDelete();
            $table->unsignedSmallInteger('prep_minutes')->default(20);
            $table->text('tip')->nullable();
            $table->timestamps();
        });

        Schema::create('starter_pack_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('starter_pack_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->string('description')->nullable();
            $table->unsignedInteger('price')->default(0);    // Rupiah, perkiraan
            $table->boolean('is_essential')->default(true);   // false = tambahan opsional
            $table->string('image')->nullable();              // path relatif terhadap public/
            $table->string('shop_url')->nullable();
            $table->unsignedSmallInteger('position')->default(0);
            $table->timestamps();
        });

        Schema::create('starter_pack_bookmarks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('starter_pack_id')->constrained()->cascadeOnDelete();
            $table->timestamps();

            $table->unique(['user_id', 'starter_pack_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('starter_pack_bookmarks');
        Schema::dropIfExists('starter_pack_items');
        Schema::dropIfExists('starter_packs');
    }
};
