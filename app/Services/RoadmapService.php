<?php

namespace App\Services;

use App\Models\DailyActivity;
use App\Models\Project;
use App\Models\User;
use App\Models\UserProject;
use App\Support\StepBlueprints;
use Illuminate\Support\Facades\DB;

class RoadmapService
{
    public const TOTAL = 6;

    /** Jawaban personalisasi -> level proyek. */
    private const LEVELS = [
        'beginner'  => 'beginner',
        'some'      => 'intermediate',
        'returning' => 'advanced',
    ];

    public function levelFor(User $user): string
    {
        return self::LEVELS[$user->experience_level] ?? 'beginner';
    }

    /** Proyek awal: hobi pertama pengguna, pada levelnya. */
    public function defaultProject(User $user): ?Project
    {
        $level = $this->levelFor($user);

        foreach ($user->hobbies()->orderBy('sort')->get() as $hobby) {
            $project = Project::where('hobby_id', $hobby->id)->where('level', $level)
                    ->orderByDesc('is_starter')->orderBy('id')->first()
                ?? Project::where('hobby_id', $hobby->id)
                    ->orderByDesc('is_starter')->orderBy('id')->first();

            if ($project) {
                return $project;
            }
        }

        return null;
    }

    /** Buat roadmap bawaan sekali saja (setelah hobi dipilih). */
    public function ensureDefault(User $user): void
    {
        if ($user->default_roadmap_created_at) {
            return;
        }

        $project = $this->defaultProject($user);

        if (! $project) {
            return;
        }

        $this->activate($user, $project);
        $user->forceFill(['default_roadmap_created_at' => now()])->save();
    }

    /** Jadikan proyek ini roadmap utama (otomatis masuk daftar). */
    public function activate(User $user, Project $project): UserProject
    {
        return DB::transaction(function () use ($user, $project) {
            UserProject::where('user_id', $user->id)->update(['is_roadmap' => false]);

            return UserProject::updateOrCreate(
                ['user_id' => $user->id, 'project_id' => $project->id],
                ['is_roadmap' => true]
            );
        });
    }

    public function addToList(User $user, Project $project): UserProject
    {
        $row = UserProject::firstOrCreate(['user_id' => $user->id, 'project_id' => $project->id]);

        $hasRoadmap = UserProject::where('user_id', $user->id)->where('is_roadmap', true)->exists();

        if (! $hasRoadmap) {
            $row->update(['is_roadmap' => true]);
        }

        return $row;
    }

    /** Hapus dari daftar. Jika itu roadmap utama, jadikan proyek lain di daftar sebagai penggantinya. */
    public function remove(User $user, Project $project): void
    {
        $row = UserProject::where('user_id', $user->id)->where('project_id', $project->id)->first();

        if (! $row) {
            return;
        }

        $wasRoadmap = $row->is_roadmap;
        $row->delete();

        if ($wasRoadmap) {
            $next = UserProject::where('user_id', $user->id)->where('steps_done', '<', self::TOTAL)->latest('updated_at')->first()
                ?? UserProject::where('user_id', $user->id)->latest('updated_at')->first();

            $next?->update(['is_roadmap' => true]);
        }
    }

    /** Enam langkah dengan status (done / current / todo) untuk pengguna ini. */
    public function steps(User $user, Project $project, ?UserProject $row): array
    {
        $done = $row?->steps_done ?? 0;

        return array_map(function (array $step) use ($row, $done) {
            $step['status'] = match (true) {
                ! $row                          => 'todo',
                $step['position'] <= $done      => 'done',
                $step['position'] === $done + 1 => 'current',
                default                         => 'todo',
            };

            return $step;
        }, StepBlueprints::build($project, $user));
    }

    public function present(User $user, UserProject $row): array
    {
        $project = $row->project;
        $steps   = $this->steps($user, $project, $row);

        return [
            'row'          => $row,
            'project'      => $project,
            'steps'        => $steps,
            'done'         => $row->steps_done,
            'total'        => self::TOTAL,
            'percent'      => (int) round($row->steps_done / self::TOTAL * 100),
            'current'      => collect($steps)->firstWhere('status', 'current'),
            'complete'     => $row->steps_done >= self::TOTAL,
            'minutes_left' => collect($steps)->where('status', '!=', 'done')->sum('minutes'),
        ];
    }

    /** Selesaikan langkah berikutnya. Mengembalikan false jika urutannya tidak sesuai. */
    public function completeStep(User $user, UserProject $row, int $position): bool
    {
        if ($row->steps_done >= self::TOTAL || $position !== $row->steps_done + 1) {
            return false;
        }

        $step    = collect(StepBlueprints::build($row->project, $user))->firstWhere('position', $position);
        $minutes = min(120, (int) ($step['minutes'] ?? 0));

        DB::transaction(function () use ($user, $row, $minutes) {
            $done = $row->steps_done + 1;

            $row->update([
                'steps_done'       => $done,
                'progress_percent' => (int) round($done / self::TOTAL * 100),
                'last_worked_at'   => now(),
                'completed_at'     => $done >= self::TOTAL ? now() : null,
            ]);

            $activity = DailyActivity::firstOrCreate(
                ['user_id' => $user->id, 'date' => today()->toDateString()],
                ['minutes' => 0]
            );
            $activity->increment('minutes', $minutes);
        });

        return true;
    }
}
