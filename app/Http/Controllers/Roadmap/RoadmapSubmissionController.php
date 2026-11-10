<?php

namespace App\Http\Controllers\Roadmap;

use App\Http\Controllers\Controller;
use App\Http\Requests\SubmissionRequest;
use App\Models\Hobby;
use App\Models\Project;
use App\Models\ProjectSubmission;
use App\Services\RoadmapService;
use App\Services\SubmissionService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class RoadmapSubmissionController extends Controller
{
    public function __construct(
        private RoadmapService $roadmap,
        private SubmissionService $submissions,
    ) {
    }

    /** "Look What You Made!": the final share step. */
    public function show(Request $request, Hobby $hobby): View|RedirectResponse
    {
        $user = $request->user();
        $project = $this->roadmap->starterProject($hobby);
        $summary = $this->roadmap->summary($user, $project);
        $shareStep = $this->roadmap->shareStep($summary['steps']);

        abort_if($shareStep === null, 404);

        if ($shareStep->state === 'locked') {
            return $this->lockedRedirect($hobby);
        }

        $submission = ProjectSubmission::with('photos')
            ->where('user_id', $user->id)
            ->where('project_id', $project->id)
            ->first();

        if ($submission?->isSubmitted()) {
            return redirect()->route('roadmap.celebration', $hobby->slug);
        }

        return view('roadmap.submission', [
            'hobby' => $hobby,
            'project' => $project,
            'summary' => $summary,
            'step' => $shareStep,
            'submission' => $submission ?? new ProjectSubmission([
                'category' => $hobby->name,
                'tags' => [],
                'is_showcase' => false,
            ]),
            'maxPhotos' => SubmissionService::MAX_PHOTOS,
        ]);
    }

    /** "Save as Draft" or "Submit My Project", decided by the `action` field. */
    public function store(SubmissionRequest $request, Hobby $hobby): RedirectResponse
    {        
        $user = $request->user();
        $project = $this->roadmap->starterProject($hobby);
        $summary = $this->roadmap->summary($user, $project);
        $shareStep = $this->roadmap->shareStep($summary['steps']);

        abort_if($shareStep === null, 404);

        if ($shareStep->state === 'locked') {
            return $this->lockedRedirect($hobby);
        }

        if ($shareStep->state === 'completed') {
            return redirect()->route('roadmap.celebration', $hobby->slug);
        }

        $submit = $request->input('action') === 'submit';

        $result = $this->submissions->save(
            $user,
            $project,
            $shareStep,
            $request->safe()->except(['action', 'photos', 'remove_photo_ids']),
            $request->file('photos', []),
            $request->input('remove_photo_ids', []),
            $submit,
        );

        if (! $submit) {
            return back()->with('status', 'Draft saved. Pick it up whenever you\'re ready.');
        }

        if ($result['badges']->isNotEmpty()) {
            session()->flash('badges', $result['badges']->pluck('name')->all());
        }

        return redirect()->route('roadmap.celebration', $hobby->slug);
    }

    /** "You did it!": badge plus a nudge toward the next project. */
    public function celebration(Request $request, Hobby $hobby): View|RedirectResponse
    {
        $user = $request->user();
        $project = $this->roadmap->starterProject($hobby);

        $submission = ProjectSubmission::with('photos')
            ->where('user_id', $user->id)
            ->where('project_id', $project->id)
            ->where('status', ProjectSubmission::STATUS_SUBMITTED)
            ->first();

        if ($submission === null) {
            return redirect()->route('roadmap.show', $hobby->slug);
        }

        $summary = $this->roadmap->summary($user, $project);
        $shareStep = $this->roadmap->shareStep($summary['steps']);

        return view('roadmap.celebration', [
            'hobby' => $hobby,
            'project' => $project,
            'summary' => $summary,
            'submission' => $submission,
            'badge' => $shareStep?->badge,
            'nextProject' => $this->suggestNextProject($project),
        ]);
    }

    /** A popular project from the same hobby first, otherwise from anywhere. */
    private function suggestNextProject(Project $current): ?Project
    {
        return Project::where('id', '!=', $current->id)
            ->where('is_popular', true)
            ->orderByRaw('hobby_id = ? desc', [$current->hobby_id])
            ->orderByDesc('learners_count')
            ->first();
    }

    public function showPhoto(
        \App\Models\SubmissionPhoto $photo
    ): \Illuminate\Http\Response {
        abort_if(
            $photo->image_data === null || $photo->mime_type === null,
            404
        );

        // Pastikan pengguna hanya bisa melihat foto miliknya.
        abort_unless(
            $photo->submission()->where('user_id', auth()->id())->exists(),
            403
        );

        return response($photo->image_data, 200, [
            'Content-Type' => $photo->mime_type,
            'Content-Disposition' => 'inline',
            'Cache-Control' => 'private, no-store',
            'X-Content-Type-Options' => 'nosniff',
        ]);
    }

    private function lockedRedirect(Hobby $hobby): RedirectResponse
    {
        return redirect()
            ->route('roadmap.show', $hobby->slug)
            ->with('status', 'Finish the earlier checkpoints first, then you can share what you made.');
    }
}
