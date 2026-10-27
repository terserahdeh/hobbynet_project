<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class StepChecklistItem extends Model
{
    protected $fillable = ['roadmap_step_id', 'position', 'label'];

    public function step(): BelongsTo
    {
        return $this->belongsTo(RoadmapStep::class, 'roadmap_step_id');
    }

    /** Users who have ticked this item. */
    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'checklist_item_user', 'step_checklist_item_id', 'user_id')
            ->withTimestamps();
    }
}
