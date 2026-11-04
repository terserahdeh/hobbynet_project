<?php

namespace App\View\Composers;

use App\Models\Hobby;
use App\Models\RoadmapStep;
use App\Models\UserStepProgress;
use App\Services\RoadmapService;
use Illuminate\View\View;

/**
 * Menyiapkan $moreHobbies untuk partial "partials.other-hobbies":
 * hobi yang BELUM dipilih pengguna, lengkap dengan roadmap pemulanya.
 * Hobi yang belum punya langkah roadmap tidak ditampilkan.
 */
class OtherHobbiesComposer
{
    public function __construct(private RoadmapService $roadmaps)
    {
    }

    public function compose(View $view): void
    {
        $user = auth()->user();

        if (! $user) {
            $view->with('moreHobbies', collect());
            return;
        }

        $chosen = $user->hobbies()->pluck('hobbies.id');

        $items = Hobby::whereNotIn('id', $chosen)
            ->orderBy('sort')
            ->get()
            ->map(function (Hobby $hobby) use ($user) {
                $project = $this->roadmaps->findStarter($hobby, $user);

                if (! $project) {
                    return null;
                }

                $steps = RoadmapStep::where('project_id', $project->id)->get(['id', 'estimated_minutes']);

                if ($steps->isEmpty()) {
                    return null;
                }

                $done = UserStepProgress::where('user_id', $user->id)
                    ->whereIn('roadmap_step_id', $steps->pluck('id'))
                    ->count();

                return [
                    'hobby'   => $hobby,
                    'project' => $project,
                    'steps'   => $steps->count(),
                    'hours'   => round($steps->sum('estimated_minutes') / 60, 1),
                    'percent' => (int) round($done / $steps->count() * 100),
                ];
            })
            ->filter()
            ->values();

        $view->with('moreHobbies', $items);
    }
}
