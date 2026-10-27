<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Badge extends Model
{
    protected $fillable = ['slug', 'name', 'description', 'icon', 'color'];

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'badge_user')
            ->withPivot(['project_id', 'earned_at'])
            ->withTimestamps();
    }
}
