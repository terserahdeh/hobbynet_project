<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // free | plus. Status Plus aktif selama plus_until masih di masa depan.
            $table->string('plan', 10)->default('free')->after('daily_goal_minutes');
            $table->timestamp('plus_until')->nullable()->after('plan');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['plan', 'plus_until']);
        });
    }
};
