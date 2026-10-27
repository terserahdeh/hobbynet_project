<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class SubmissionPhoto extends Model
{
    protected $fillable = ['project_submission_id', 'path', 'position'];

    public function submission(): BelongsTo
    {
        return $this->belongsTo(ProjectSubmission::class, 'project_submission_id');
    }

    protected function url(): Attribute
    {
        return Attribute::get(
            fn () => url('storage/' . $this->path)
        );
    }

    protected $appends = ['url'];
}
