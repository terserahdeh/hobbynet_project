<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserStepProgress extends Model
{
    protected $table = 'user_step_progress';

    protected $fillable = ['user_id', 'roadmap_step_id', 'completed_at'];

    protected function casts(): array
    {
        return ['completed_at' => 'datetime'];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function step(): BelongsTo
    {
        return $this->belongsTo(RoadmapStep::class, 'roadmap_step_id');
    }
}
