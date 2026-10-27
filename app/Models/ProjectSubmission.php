<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ProjectSubmission extends Model
{
    public const STATUS_DRAFT = 'draft';
    public const STATUS_SUBMITTED = 'submitted';

    protected $fillable = [
        'user_id', 'project_id', 'title', 'reflection', 'category',
        'tags', 'is_showcase', 'status', 'submitted_at',
    ];

    protected function casts(): array
    {
        return [
            'tags' => 'array',
            'is_showcase' => 'boolean',
            'submitted_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function photos(): HasMany
    {
        return $this->hasMany(SubmissionPhoto::class, 'project_submission_id')->orderBy('position');
    }

    public function isSubmitted(): bool
    {
        return $this->status === self::STATUS_SUBMITTED;
    }
}
