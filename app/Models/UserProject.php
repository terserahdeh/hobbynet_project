<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/** Satu baris = satu proyek di daftar (to-do) milik seorang pengguna. */
class UserProject extends Model
{
    protected $fillable = [
        'user_id', 'project_id', 'progress_percent', 'steps_done', 'is_roadmap', 'last_worked_at', 'completed_at',
    ];

    protected $casts = [
        'progress_percent' => 'integer',
        'steps_done'       => 'integer',
        'is_roadmap'       => 'boolean',
        'last_worked_at'   => 'datetime',
        'completed_at'     => 'datetime',
    ];

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
