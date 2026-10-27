<?php

namespace App\Http\Controllers\Roadmap;

use App\Http\Controllers\Controller;
use App\Models\Hobby;
use App\Models\Project;
use App\Services\RoadmapService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Contracts\View\View;

class RoadmapController extends Controller
{
    public function __construct(private RoadmapService $roadmap)
    {
    }

    /** /roadmap sends the user to the roadmap they last worked on. */
    public function index(Request $request): RedirectResponse
    {
        $hobby = $this->roadmap->defaultHobby($request->user());

        abort_if($hobby === null, 404);

        return redirect()->route('roadmap.show', $hobby->slug);
    }

    /**
     * Entry point for the home screen. Sends a starter project to the right
     * place in its roadmap: the overview for brand-new users, otherwise
     * straight to their current step. Anything without a roadmap falls back
     * to the normal project page.
     */
    public function project(Request $request, Project $project): RedirectResponse
    {
        $hobby = ($project->is_starter && $project->hobby_id) ? Hobby::find($project->hobby_id) : null;
        $summary = $hobby ? $this->roadmap->summary($request->user(), $project) : null;

        if (! $hobby || $summary['total'] === 0) {
            return redirect()->route('projects.show', $project);
        }

        $active = $summary['active'];

        if ($summary['completed'] === 0 || $active === null) {
            return redirect()->route('roadmap.show', $hobby->slug);
        }

        return $active->isShare()
            ? redirect()->route('roadmap.submission.show', $hobby->slug)
            : redirect()->route('roadmap.steps.show', [$hobby->slug, $active->slug]);
    }

    /** "Your Crochet Journey": progress header plus the checkpoint timeline. */
    public function show(Request $request, Hobby $hobby): View
    {
        $user = $request->user();
        $project = $this->roadmap->starterProject($hobby);

        $this->roadmap->begin($user, $project);

        return view('roadmap.show', [
            'hobby' => $hobby,
            'project' => $project,
            'summary' => $this->roadmap->summary($user, $project),
            'otherHobbies' => $this->roadmap->userHobbies($user)->where('id', '!=', $hobby->id)->values(),
        ]);
    }
}
