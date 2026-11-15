<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\RoadmapStep;
use App\Models\StepChecklistItem;
use App\Models\StepMaterial;
use App\Services\RoadmapService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

/**
 * Proyek berpemandu: halaman detail + langkah satu per satu, TANPA tampilan roadmap.
 * Memakai tabel dan RoadmapService yang sama dengan roadmap (roadmap_steps, user_step_progress, dst).
 * Proyek starter (roadmap) tetap diarahkan ke halaman roadmap hobinya.
 */
class GuidedProjectController extends Controller
{
    public function __construct(private RoadmapService $roadmap)
    {
    }

    /** Detail proyek: info, bahan, yang dipelajari, pratinjau langkah 1. */
    public function show(Request $request, Project $project): View|RedirectResponse
    {
        $user = $request->user();
        $project->loadMissing('hobby');

        $stepIds = RoadmapStep::where('project_id', $project->id)->pluck('id');

        if ($project->is_starter && $project->hobby && $stepIds->isNotEmpty()) {
            return redirect()->route('roadmap.show', $project->hobby->slug);
        }

        $summary = $this->roadmap->summary($user, $project);

        $materials = StepMaterial::whereIn('roadmap_step_id', $stepIds)
            ->orderBy('roadmap_step_id')->orderBy('position')->get()
            ->unique('name')->values();

        $ready = DB::table('project_material_ready')
            ->where('user_id', $user->id)->where('project_id', $project->id)
            ->pluck('name')->all();

        $learn = $project->learnings;
        if (is_string($learn)) {
            $learn = json_decode($learn, true);
        }

        $first = $summary['steps']->first();

        return view('projects.show', [
            'project' => $project,
            'summary' => $summary,
            'started' => DB::table('user_projects')->where('user_id', $user->id)->where('project_id', $project->id)->exists(),
            'materials' => $materials,
            'ready' => $ready,
            'learnings' => is_array($learn) ? $learn : [],
            'first' => $first ? $first->load('instructions') : null,
            'shopUrl' => config('hobbynet.shop_search_url'),
        ]);
    }

    /** "Start Project" / "Continue Project". */
    public function start(Request $request, Project $project): RedirectResponse
    {
        $this->abortIfRoadmapProject($project);

        $this->roadmap->begin($request->user(), $project);
        $summary = $this->roadmap->summary($request->user(), $project);
        $target = $summary['active'] ?? $summary['steps']->first();

        abort_if($target === null, 404);

        return redirect()->route('projects.steps.show', [$project->slug, $target->slug]);
    }

    /** Satu langkah: instruksi, bahan, tip, checklist. */
    public function step(Request $request, Project $project, string $stepSlug): View|RedirectResponse
    {
        if ($redirect = $this->roadmapRedirect($project)) {
            return $redirect;
        }

        [$summary, $step] = $this->locate($request, $project, $stepSlug);

        if ($step->state === 'locked') {
            return $this->lockedRedirect($project);
        }

        $this->roadmap->begin($request->user(), $project);
        $step->load(['materials', 'instructions', 'checklistItems']);

        $steps = $summary['steps'];
        $user = $request->user();

        return view('projects.step', [
            'project' => $project,
            'summary' => $summary,
            'step' => $step,
            'steps' => $steps,
            'previous' => $steps->last(fn ($s) => $s->position < $step->position),
            'next' => $steps->first(fn ($s) => $s->position > $step->position),
            'checkedIds' => $this->roadmap->checkedItemIds($user, $step),
            'goal' => [
                'goal' => max(1, (int) ($user->daily_goal_minutes ?: 45)),
                'minutes' => (int) DB::table('daily_activities')
                    ->where('user_id', $user->id)->where('date', today()->toDateString())->value('minutes'),
            ],
        ]);
    }

    /** Centang atau batalkan satu item checklist. */
    public function toggleChecklist(Request $request, Project $project, string $stepSlug, StepChecklistItem $item): JsonResponse|RedirectResponse
    {
        $this->abortIfRoadmapProject($project);

        [, $step] = $this->locate($request, $project, $stepSlug);

        if ($step->state === 'locked') {
            return $this->lockedRedirect($project);
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

    /** "Mark as Complete": seluruh checklist harus tercentang, lalu lanjut. */
    public function complete(Request $request, Project $project, string $stepSlug): RedirectResponse
    {
        $this->abortIfRoadmapProject($project);

        [$summary, $step] = $this->locate($request, $project, $stepSlug);

        if ($step->state === 'locked') {
            return $this->lockedRedirect($project);
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
            return redirect()->route('projects.show', $project->slug)
                ->with('status', 'You finished ' . $project->title . '! One small step at a time got you here.');
        }

        return redirect()->route('projects.steps.show', [$project->slug, $next->slug]);
    }

    /** Tandai bahan "sudah punya" / batalkan. */
    public function toggleMaterial(Request $request, Project $project): RedirectResponse
    {
        $this->abortIfRoadmapProject($project);

        $name = $request->validate(['name' => ['required', 'string', 'max:150']])['name'];

        $stepIds = RoadmapStep::where('project_id', $project->id)->pluck('id');
        abort_unless(StepMaterial::whereIn('roadmap_step_id', $stepIds)->where('name', $name)->exists(), 404);

        $key = ['user_id' => $request->user()->id, 'project_id' => $project->id, 'name' => $name];

        if (! DB::table('project_material_ready')->where($key)->delete()) {
            DB::table('project_material_ready')->insertOrIgnore($key + ['created_at' => now(), 'updated_at' => now()]);
        }

        return back();
    }

    /** @return array{0: array, 1: RoadmapStep} */
    private function locate(Request $request, Project $project, string $stepSlug): array
    {
        $summary = $this->roadmap->summary($request->user(), $project);
        $step = $summary['steps']->firstWhere('slug', $stepSlug);

        abort_if($step === null, 404);

        return [$summary, $step];
    }

    /** Proyek starter punya halaman roadmap sendiri. */
    private function roadmapRedirect(Project $project): ?RedirectResponse
    {
        $project->loadMissing('hobby');

        if ($project->is_starter && $project->hobby && RoadmapStep::where('project_id', $project->id)->exists()) {
            return redirect()->route('roadmap.show', $project->hobby->slug);
        }

        return null;
    }

    private function abortIfRoadmapProject(Project $project): void
    {
        $project->loadMissing('hobby');

        abort_if(
            $project->is_starter && $project->hobby && RoadmapStep::where('project_id', $project->id)->exists(),
            404
        );
    }

    private function lockedRedirect(Project $project): RedirectResponse
    {
        return redirect()
            ->route('projects.show', $project->slug)
            ->with('status', 'That step opens once the one before it is done. One small step at a time.');
    }
}
