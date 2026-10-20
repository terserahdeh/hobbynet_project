<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Hobby extends Model
{
    protected $fillable = ['slug', 'name', 'tagline', 'first_project', 'color', 'sort'];

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class)->withTimestamps();
    }

    public function projects(): HasMany
    {
        return $this->hasMany(Project::class);
    }

    /** "Merajut (Crochet)" menjadi "Merajut", untuk chip di beranda. */
    public function getShortNameAttribute(): string
    {
        return Str::before($this->name, ' (');
    }
}
