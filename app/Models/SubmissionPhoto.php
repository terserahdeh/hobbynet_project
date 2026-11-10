<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SubmissionPhoto extends Model
{
    protected $fillable = [
        'project_submission_id',
        'path',
        'position',
        'image_data',
        'mime_type',
    ];

    protected $hidden = ['image_data'];

    protected $appends = ['url'];

    public function submission(): BelongsTo
    {
        return $this->belongsTo(
            ProjectSubmission::class,
            'project_submission_id'
        );
    }

    protected function url(): Attribute
    {
        return Attribute::get(
            fn () => route('submission-photos.show', [
                'photo' => $this->id,
            ])
        );
    }
}
