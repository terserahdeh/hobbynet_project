<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StepMaterial extends Model
{
    protected $fillable = ['roadmap_step_id', 'position', 'kind', 'name', 'spec', 'is_optional'];

    protected function casts(): array
    {
        return ['is_optional' => 'boolean'];
    }

    public function step(): BelongsTo
    {
        return $this->belongsTo(RoadmapStep::class, 'roadmap_step_id');
    }
}
