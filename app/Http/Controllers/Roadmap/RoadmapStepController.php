<?php

namespace App\Http\Controllers\Roadmap;

use App\Http\Controllers\Controller;
use App\Models\Hobby;
use App\Models\RoadmapStep;
use App\Models\StepChecklistItem;
use App\Services\RoadmapService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class RoadmapStepController extends Controller
{
    public function __construct(private RoadmapService $roadmap)
    {
    }

    /** One checkpoint: instructions, materials, checklist. */
    public function show(Request $request, Hobby $hobby, string $stepSlug): View|RedirectResponse
    {
        [$project, $summary, $step] = $this->locate($request, $hobby, $stepSlug);

        if ($step->state === 'locked') {
            return $this->lockedRedirect($hobby);
        }

        if ($step->isShare()) {
            return redirect()->route('roadmap.submission.show', $hobby->slug);
        }

        $step->load(['materials', 'instructions', 'checklistItems']);

        $steps = $summary['steps'];

        return view('roadmap.step', [
            'hobby' => $hobby,
            'project' => $project,
            'summary' => $summary,
            'step' => $step,
            'steps' => $steps,
            'previous' => $steps->last(fn ($s) => $s->position < $step->position),
            'next' => $steps->first(fn ($s) => $s->position > $step->position),
            'checkedIds' => $this->roadmap->checkedItemIds($request->user(), $step),
        ]);
    }

    /** Tick or untick a checklist item. */
    public function toggleChecklist(Request $request, Hobby $hobby, string $stepSlug, StepChecklistItem $item): JsonResponse|RedirectResponse
    {
        [, , $step] = $this->locate($request, $hobby, $stepSlug);

        if ($step->state === 'locked') {
            return $this->lockedRedirect($hobby);
        }

        abort_unless($item->roadmap_step_id === $step->id, 404);

        $result = $item->users()->toggle($request->user()->id);
        $checked = count($result['attached']) > 0;

        if ($request->expectsJson()) {
            return response()->json([
                'checked' => $checked,
                'checked_count' => count($this->roadmap->checkedItemIds($request->user(), $step)),
            ]);
        }

        return back();
    }

    /** "Mark as Complete": needs the whole checklist ticked, then moves on. */
    public function complete(Request $request, Hobby $hobby, string $stepSlug): RedirectResponse
    {
        [, $summary, $step] = $this->locate($request, $hobby, $stepSlug);

        if ($step->state === 'locked') {
            return $this->lockedRedirect($hobby);
        }

        if ($step->isShare()) {
            return redirect()->route('roadmap.submission.show', $hobby->slug);
        }

        $user = $request->user();

        if ($step->state !== 'completed') {
            $required = $step->checklistItems()->pluck('id');
            $checked = collect($this->roadmap->checkedItemIds($user, $step));

            if ($required->diff($checked)->isNotEmpty()) {
                throw ValidationException::withMessages([
                    'checklist' => 'Tick off each item on the checklist when you\'re ready to move on.',
                ]);
            }

            $badges = $this->roadmap->completeStep($user, $step);

            if ($badges->isNotEmpty()) {
                session()->flash('badges', $badges->pluck('name')->all());
            }
        }

        $next = $summary['steps']->first(fn ($s) => $s->position > $step->position);

        if ($next === null) {
            return redirect()->route('roadmap.show', $hobby->slug);
        }

        return $next->isShare()
            ? redirect()->route('roadmap.submission.show', $hobby->slug)
            : redirect()->route('roadmap.steps.show', [$hobby->slug, $next->slug]);
    }

    /** @return array{0: \App\Models\Project, 1: array, 2: RoadmapStep} */
    private function locate(Request $request, Hobby $hobby, string $stepSlug): array
    {
        $project = $this->roadmap->starterProject($hobby);
        $summary = $this->roadmap->summary($request->user(), $project);
        $step = $summary['steps']->firstWhere('slug', $stepSlug);

        abort_if($step === null, 404);

        return [$project, $summary, $step];
    }

    private function lockedRedirect(Hobby $hobby): RedirectResponse
    {
        return redirect()
            ->route('roadmap.show', $hobby->slug)
            ->with('status', 'That checkpoint opens once the one before it is done. One small step at a time.');
    }
}
