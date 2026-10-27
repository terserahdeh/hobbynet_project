<?php

namespace App\Services;

use App\Models\Badge;
use App\Models\Hobby;
use App\Models\Project;
use App\Models\RoadmapStep;
use App\Models\User;
use App\Models\UserStepProgress;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class RoadmapService
{
    public function __construct(private BadgeService $badges)
    {
    }

    /** The beginner project that powers a hobby's roadmap. */
    public function starterProject(Hobby $hobby): Project
    {
        return Project::where('hobby_id', $hobby->id)
            ->where('is_starter', true)
            ->firstOrFail();
    }

    /** Hobbies the user picked during onboarding (for "Switch Hobby"). */
    public function userHobbies(User $user): Collection
    {
        return Hobby::whereIn('id', function ($q) use ($user) {
            $q->select('hobby_id')->from('hobby_user')->where('user_id', $user->id);
        })->orderBy('sort')->get();
    }

    /** Hobby to open when the user visits /roadmap: last worked on, else first selected. */
    public function defaultHobby(User $user): ?Hobby
    {
        $hobbyId = DB::table('user_projects')
            ->join('projects', 'projects.id', '=', 'user_projects.project_id')
            ->where('user_projects.user_id', $user->id)
            ->where('projects.is_starter', true)
            ->whereNotNull('projects.hobby_id')
            ->orderByDesc('user_projects.last_worked_at')
            ->value('projects.hobby_id');

        if ($hobbyId && ($hobby = Hobby::find($hobbyId))) {
            return $hobby;
        }

        return $this->userHobbies($user)->first() ?? Hobby::orderBy('sort')->first();
    }

    /** Make sure a user_projects row exists as soon as the user opens a roadmap. */
    public function begin(User $user, Project $project): void
    {
        DB::table('user_projects')->insertOrIgnore([
            'user_id' => $user->id,
            'project_id' => $project->id,
            'progress_percent' => 0,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    /**
     * Every step in order, each tagged with state (completed | active | locked).
     * Steps unlock one at a time: the first unfinished step is "active".
     */
    public function steps(User $user, Project $project): Collection
    {
        $steps = RoadmapStep::where('project_id', $project->id)
            ->with('badge')
            ->orderBy('position')
            ->get();

        $done = UserStepProgress::where('user_id', $user->id)
            ->whereIn('roadmap_step_id', $steps->pluck('id'))
            ->get()
            ->keyBy('roadmap_step_id');

        $activeAssigned = false;

        return $steps->map(function (RoadmapStep $step) use ($done, &$activeAssigned) {
            if ($done->has($step->id)) {
                $step->setAttribute('state', 'completed');
                $step->setAttribute('completed_at', $done[$step->id]->completed_at);
            } elseif (! $activeAssigned) {
                $step->setAttribute('state', 'active');
                $activeAssigned = true;
            } else {
                $step->setAttribute('state', 'locked');
            }

            return $step;
        });
    }

    /** Numbers for the progress header on the overview and step pages. */
    public function summary(User $user, Project $project): array
    {
        $steps = $this->steps($user, $project);
        $total = $steps->count();
        $completed = $steps->where('state', 'completed')->count();

        return [
            'steps' => $steps,
            'total' => $total,
            'completed' => $completed,
            'percent' => $total > 0 ? (int) round($completed / $total * 100) : 0,
            'total_minutes' => (int) $steps->sum('estimated_minutes'),
            'remaining_minutes' => (int) $steps->where('state', '!=', 'completed')->sum('estimated_minutes'),
            'active' => $steps->firstWhere('state', 'active'),
            'is_complete' => $total > 0 && $completed === $total,
        ];
    }

    public function shareStep(Collection $steps): ?RoadmapStep
    {
        return $steps->firstWhere('type', RoadmapStep::TYPE_SHARE);
    }

    /** IDs of checklist items the user has ticked for a step. */
    public function checkedItemIds(User $user, RoadmapStep $step): array
    {
        return DB::table('checklist_item_user')
            ->where('user_id', $user->id)
            ->whereIn('step_checklist_item_id', function ($q) use ($step) {
                $q->select('id')->from('step_checklist_items')->where('roadmap_step_id', $step->id);
            })
            ->pluck('step_checklist_item_id')
            ->all();
    }

    /**
     * Mark a step done. Safe to call twice.
     *
     * @return Collection<int, Badge> badges earned by this call (empty if none)
     */
    public function completeStep(User $user, RoadmapStep $step): Collection
    {
        return DB::transaction(function () use ($user, $step) {
            $earned = collect();

            $progress = UserStepProgress::firstOrCreate(
                ['user_id' => $user->id, 'roadmap_step_id' => $step->id],
                ['completed_at' => now()],
            );

            if (! $progress->wasRecentlyCreated) {
                return $earned;
            }

            $this->logMinutes($user, $step->estimated_minutes);

            if ($step->badge_id && ($badge = $step->badge ?? Badge::find($step->badge_id))
                && $this->badges->award($user, $badge, $step->project_id)) {
                $earned->push($badge);
            }

            $percent = $this->syncProgress($user, $step->project_id);

            if ($percent === 100) {
                $final = $this->badges->awardBySlug($user, 'course-completer', $step->project_id);
                if ($final) {
                    $earned->push($final);
                }
            }

            return $earned;
        });
    }

    /** Recalculate user_projects.progress_percent; returns the new percent. */
    private function syncProgress(User $user, int $projectId): int
    {
        $stepIds = RoadmapStep::where('project_id', $projectId)->pluck('id');
        $total = $stepIds->count();
        $done = UserStepProgress::where('user_id', $user->id)->whereIn('roadmap_step_id', $stepIds)->count();
        $percent = $total > 0 ? (int) round($done / $total * 100) : 0;

        DB::table('user_projects')->insertOrIgnore([
            'user_id' => $user->id,
            'project_id' => $projectId,
            'progress_percent' => 0,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::table('user_projects')
            ->where('user_id', $user->id)
            ->where('project_id', $projectId)
            ->update([
                'progress_percent' => $percent,
                'last_worked_at' => now(),
                'updated_at' => now(),
            ]);

        return $percent;
    }

    /** Add minutes to today's total (feeds the "Daily Goal" card). */
    private function logMinutes(User $user, int $minutes): void
    {
        $today = today()->toDateString();

        $updated = DB::table('daily_activities')
            ->where('user_id', $user->id)
            ->where('date', $today)
            ->update([
                'minutes' => DB::raw('LEAST(minutes + ' . (int) $minutes . ', 65535)'),
                'updated_at' => now(),
            ]);

        if (! $updated) {
            DB::table('daily_activities')->insertOrIgnore([
                'user_id' => $user->id,
                'date' => $today,
                'minutes' => min($minutes, 65535),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
