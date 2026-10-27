<?php

namespace App\Services;

use App\Models\Badge;
use App\Models\User;
use App\Models\RoadmapStep;
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

    /**
     * Lencana berikutnya yang paling dekat: langkah roadmap berlencana pertama
     * yang belum diraih, di proyek yang terakhir dikerjakan user.
     *
     * @return array{badge: Badge, title: string, value: int, target: int}|null
     */
    public function nextFor(User $user): ?array
    {
        $projectId = DB::table('user_projects')
            ->join('roadmap_steps', 'roadmap_steps.project_id', '=', 'user_projects.project_id')
            ->where('user_projects.user_id', $user->id)
            ->orderByDesc('user_projects.last_worked_at')
            ->orderByDesc('user_projects.id')
            ->value('user_projects.project_id')
            ?? RoadmapStep::query()->orderBy('project_id')->value('project_id');

        if (! $projectId) {
            return null;
        }

        $steps = RoadmapStep::with('badge')->where('project_id', $projectId)->orderBy('position')->get();
        $done = DB::table('user_step_progress')->where('user_id', $user->id)
            ->whereIn('roadmap_step_id', $steps->pluck('id'))->pluck('roadmap_step_id')->all();
        $earned = DB::table('badge_user')->where('user_id', $user->id)->pluck('badge_id')->all();

        $target = $steps->first(fn ($s) => $s->badge && ! in_array($s->badge_id, $earned, true));
        if (! $target) {
            return null;
        }

        $value = $steps->filter(fn ($s) => $s->position <= $target->position && in_array($s->id, $done, true))->count();

        return [
            'badge' => $target->badge,
            'title' => $target->title,
            'value' => $value,
            'target' => $steps->filter(fn ($s) => $s->position <= $target->position)->count(),
        ];
    }
}
