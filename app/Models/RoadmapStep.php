<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Runtime attributes set by RoadmapService::steps():
 *  - state:        completed | active | locked
 *  - completed_at: when the current user finished it (completed only)
 */
class RoadmapStep extends Model
{
    public const TYPE_SHARE = 'share';

    protected $fillable = [
        'project_id', 'position', 'slug', 'title', 'summary', 'objective',
        'type', 'estimated_minutes', 'encouragement', 'icon', 'badge_id',
    ];

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function badge(): BelongsTo
    {
        return $this->belongsTo(Badge::class);
    }

    public function materials(): HasMany
    {
        return $this->hasMany(StepMaterial::class)->orderBy('position');
    }

    public function instructions(): HasMany
    {
        return $this->hasMany(StepInstruction::class)->orderBy('position');
    }

    public function checklistItems(): HasMany
    {
        return $this->hasMany(StepChecklistItem::class)->orderBy('position');
    }

    public function isShare(): bool
    {
        return $this->type === self::TYPE_SHARE;
    }
}
