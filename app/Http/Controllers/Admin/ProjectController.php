<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\ProjectRequest;
use App\Models\Hobby;
use App\Models\Project;
use App\Models\RoadmapStep;
use App\Services\Admin\RoadmapBuilder;
use App\Support\UniqueSlug;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProjectController extends Controller
{
    public function __construct(private RoadmapBuilder $builder)
    {
    }

    public function index(): View
    {
        return view('admin.projects.index', [
            'projects' => Project::orderByDesc('is_starter')->orderBy('title')->get(),
            'hobbies' => Hobby::orderBy('sort')->get()->keyBy('id'),
            'stepCounts' => RoadmapStep::selectRaw('project_id, count(*) as total')->groupBy('project_id')->pluck('total', 'project_id'),
        ]);
    }

    public function create(): View
    {
        return view('admin.projects.form', [
            'project' => (new Project)->forceFill([
                'level' => 'beginner', 'duration_minutes' => 30, 'color' => 'pink', 'icon' => 'default',
            ]),
            'hobbies' => Hobby::orderBy('sort')->orderBy('name')->get(),
            'problems' => [],
            'stepCount' => 0,
        ]);
    }

    public function store(ProjectRequest $request): RedirectResponse
    {
        $data = $this->clean($request->validated());

        $project = new Project;
        $project->forceFill($data + [
            'slug' => UniqueSlug::make($data['title'], 'projects'),
            'roadmap_published' => false,
        ])->save();

        return redirect()->route('admin.projects.edit', $project)
            ->with('status', $project->is_starter
                ? 'Project created. Now build its roadmap by adding steps.'
                : 'Project created.');
    }

    public function edit(Project $project): View
    {
        return view('admin.projects.form', [
            'project' => $project,
            'hobbies' => Hobby::orderBy('sort')->orderBy('name')->get(),
            'problems' => $this->builder->problems($project),
            'stepCount' => RoadmapStep::where('project_id', $project->id)->count(),
            'learners' => $this->builder->learnerCounts($project),
        ]);
    }

    public function update(ProjectRequest $request, Project $project): RedirectResponse
    {
        $project->forceFill($this->clean($request->validated()))->save();

        // A roadmap only exists for a hobby's starter project.
        if ($project->roadmap_published && (! $project->is_starter || ! $project->hobby_id)) {
            $project->forceFill(['roadmap_published' => false])->save();

            return back()->with('warning', 'Saved, but the roadmap was hidden from learners because this is no longer a hobby starter project.');
        }

        return back()->with('status', 'Project saved.');
    }

    public function destroy(Project $project): RedirectResponse
    {
        $title = $project->title;

        RoadmapStep::where('project_id', $project->id)->whereNotNull('image')->pluck('image')
            ->each(fn ($path) => Storage::disk('public')->delete($path));

        $project->delete(); // steps, progress and submissions cascade

        return redirect()->route('admin.projects.index')->with('status', "“{$title}” was deleted.");
    }

    /** Show or hide the roadmap on the learner side. */
    public function publish(Request $request, Project $project): RedirectResponse
    {
        if (! $request->boolean('publish')) {
            $project->forceFill(['roadmap_published' => false])->save();

            return back()->with('status', 'The roadmap is hidden from learners now.');
        }

        $problems = $this->builder->problems($project);

        if ($problems !== []) {
            return back()->with('warning', 'Not ready to publish yet. See the checklist below.');
        }

        $project->forceFill(['roadmap_published' => true])->save();

        return back()->with('status', 'Published. Learners can now follow this roadmap.');
    }

    /** @param array<string, mixed> $data */
    private function clean(array $data): array
    {
        $data['icon'] = filled($data['icon'] ?? null) ? $data['icon'] : 'default';
        $data['image'] = filled($data['image'] ?? null) ? $data['image'] : null;

        return $data;
    }
}
