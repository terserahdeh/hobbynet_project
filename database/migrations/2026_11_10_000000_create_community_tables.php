<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration {
 public function up(): void {
  Schema::create('community_likes', function(Blueprint $t){$t->id();$t->foreignId('user_id')->constrained()->cascadeOnDelete();$t->foreignId('project_submission_id')->constrained()->cascadeOnDelete();$t->timestamps();$t->unique(['user_id','project_submission_id']);});
  Schema::create('community_saves', function(Blueprint $t){$t->id();$t->foreignId('user_id')->constrained()->cascadeOnDelete();$t->foreignId('project_submission_id')->constrained()->cascadeOnDelete();$t->timestamps();$t->unique(['user_id','project_submission_id']);});
  Schema::create('community_comments', function(Blueprint $t){$t->id();$t->foreignId('user_id')->constrained()->cascadeOnDelete();$t->foreignId('project_submission_id')->constrained()->cascadeOnDelete();$t->foreignId('parent_id')->nullable()->constrained('community_comments')->cascadeOnDelete();$t->text('body');$t->timestamps();});
  Schema::create('community_notifications', function(Blueprint $t){$t->id();$t->foreignId('user_id')->constrained()->cascadeOnDelete();$t->foreignId('actor_id')->constrained('users')->cascadeOnDelete();$t->foreignId('project_submission_id')->constrained()->cascadeOnDelete();$t->string('type',20);$t->text('message');$t->timestamp('read_at')->nullable();$t->timestamps();$t->index(['user_id','read_at']);});
 }
 public function down(): void {foreach(['community_notifications','community_comments','community_saves','community_likes'] as $table) Schema::dropIfExists($table);}
};
