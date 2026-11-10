<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Badge;
use App\Models\Hobby;
use App\Models\Project;
use App\Models\ProjectSubmission;
use App\Models\RoadmapStep;
use App\Models\UserStepProgress;
use Illuminate\Contracts\View\View;

class DashboardController extends Controller
{
    public function __invoke(): View
    {
        $stepCounts = RoadmapStep::selectRaw('project_id, count(*) as total')
            ->groupBy('project_id')
            ->pluck('total', 'project_id');

        // Roadmaps: every starter project, plus anything that already has steps.
        $roadmaps = Project::where('is_starter', true)
            ->orWhereIn('id', $stepCounts->keys())
            ->orderBy('title')
            ->get();

        return view('admin.dashboard', [
            'stats' => [
                'hobbies' => Hobby::count(),
                'roadmaps' => Project::where('roadmap_published', true)->count(),
                'steps' => RoadmapStep::count(),
                'badges' => Badge::count(),
                'learners' => UserStepProgress::distinct()->count('user_id'),
                'submissions' => ProjectSubmission::where('status', ProjectSubmission::STATUS_SUBMITTED)->count(),
            ],
            'roadmaps' => $roadmaps,
            'stepCounts' => $stepCounts,
            'hobbies' => Hobby::orderBy('sort')->get()->keyBy('id'),
        ]);
    }
}
