<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\RoadmapStepRequest;
use App\Models\Badge;
use App\Models\Hobby;
use App\Models\Project;
use App\Models\RoadmapStep;
use App\Services\Admin\RoadmapBuilder;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;

class RoadmapStepController extends Controller
{
    public function __construct(private RoadmapBuilder $builder)
    {
    }

    /** The roadmap's steps, in the order learners will meet them. */
    public function index(Project $project): View
    {
        $steps = RoadmapStep::where('project_id', $project->id)
            ->with('badge')
            ->withCount(['materials', 'instructions', 'checklistItems'])
            ->orderBy('position')
            ->get();

        return view('admin.steps.index', [
            'project' => $project,
            'hobby' => $project->hobby_id ? Hobby::find($project->hobby_id) : null,
            'steps' => $steps,
            'problems' => $this->builder->problems($project),
            'learners' => $this->builder->learnerCounts($project),
        ]);
    }

    public function create(Project $project): View
    {
        return view('admin.steps.form', [
            'project' => $project,
            'step' => new RoadmapStep(['type' => 'lesson', 'estimated_minutes' => 15]),
            'badges' => Badge::orderBy('name')->get(),
            'learners' => null,
        ]);
    }

    public function store(RoadmapStepRequest $request, Project $project): RedirectResponse
    {
        $step = $this->builder->saveStep($project, null, $request->validated(), $request->file('image'));

        return $this->done($project, "“{$step->title}” was added to the roadmap.");
    }

    public function edit(RoadmapStep $step): View
    {
        $project = Project::findOrFail($step->project_id);
        $step->load(['materials', 'instructions', 'checklistItems']);

        return view('admin.steps.form', [
            'project' => $project,
            'step' => $step,
            'badges' => Badge::orderBy('name')->get(),
            'learners' => $this->builder->learnerCounts($project),
        ]);
    }

    public function update(RoadmapStepRequest $request, RoadmapStep $step): RedirectResponse
    {
        $project = Project::findOrFail($step->project_id);
        $step = $this->builder->saveStep($project, $step, $request->validated(), $request->file('image'));

        return $this->done($project, "“{$step->title}” was saved.");
    }

    public function destroy(RoadmapStep $step): RedirectResponse
    {
        $project = Project::findOrFail($step->project_id);
        $title = $step->title;

        $this->builder->deleteStep($step);

        return $this->done($project, "“{$title}” was deleted.");
    }

    public function move(RoadmapStep $step, string $direction): RedirectResponse
    {
        $this->builder->moveStep($step, $direction);

        return redirect()->route('admin.projects.steps.index', $step->project_id);
    }

    private function done(Project $project, string $message): RedirectResponse
    {
        $redirect = redirect()->route('admin.projects.steps.index', $project)->with('status', $message);

        if ($this->builder->unpublishIfBroken($project)) {
            $redirect->with('warning', 'This change left the roadmap incomplete, so it was hidden from learners. Fix the items below, then publish again.');
        }

        return $redirect;
    }
}
