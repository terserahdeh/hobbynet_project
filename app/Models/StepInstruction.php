<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StepInstruction extends Model
{
    protected $fillable = ['roadmap_step_id', 'position', 'body', 'image', 'image_caption'];
    protected $hidden = ['image'];

    public function step(): BelongsTo
    {
        return $this->belongsTo(RoadmapStep::class, 'roadmap_step_id');
    }
}
