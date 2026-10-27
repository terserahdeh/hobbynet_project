<?php

namespace App\Services;

use App\Models\Badge;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class BadgeService
{
    /**
     * Give a badge to a user once. Returns true only the first time.
     */
    public function award(User $user, Badge $badge, ?int $projectId = null): bool
    {
        $inserted = DB::table('badge_user')->insertOrIgnore([
            'user_id' => $user->id,
            'badge_id' => $badge->id,
            'project_id' => $projectId,
            'earned_at' => now(),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return $inserted > 0;
    }

    public function awardBySlug(User $user, string $slug, ?int $projectId = null): ?Badge
    {
        $badge = Badge::where('slug', $slug)->first();

        if ($badge && $this->award($user, $badge, $projectId)) {
            return $badge;
        }

        return null;
    }
}
