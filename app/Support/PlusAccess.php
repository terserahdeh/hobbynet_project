<?php

namespace App\Support;

use App\Models\Project;
use App\Models\User;

/** Aturan akses HobbyNet Plus untuk proyek. */
class PlusAccess
{
    public static function isPremium(Project $project): bool
    {
        return (bool) $project->is_premium;
    }

    /** true = pengguna hanya boleh melihat pratinjau proyek ini. */
    public static function locked(Project $project, ?User $user): bool
    {
        return self::isPremium($project) && ! ($user?->isPlus() ?? false);
    }
}
