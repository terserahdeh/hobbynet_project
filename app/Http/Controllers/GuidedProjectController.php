<?php

namespace App\Http\Controllers;

use App\Http\Requests\SubmissionRequest;
use App\Models\Project;
use App\Models\ProjectSubmission;
use App\Models\RoadmapStep;
use App\Models\StepChecklistItem;
use App\Models\StepMaterial;
use App\Models\User;
use App\Services\RoadmapService;
use App\Services\SubmissionService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

/**
 * Halaman proyek: detail + langkah berpemandu satu per satu (tanpa tampilan roadmap).
 * Memakai tabel dan RoadmapService yang sama dengan roadmap (roadmap_steps, user_step_progress, dst).
 * Hanya starter yang sedang menjadi roadmap utama pengguna (satu per hobi) yang dialihkan ke halaman roadmap.
 * Starter level/hobi lain dan proyek biasa dibuka di sini sebagai proyek berpemandu.
 * Langkah terakhir ("Share") memakai SubmissionService dan form yang sama dengan roadmap:
 * simpan pribadi, atau centang "Bagikan ke Komunitas" agar tampil di halaman Komunitas.
 */
class GuidedProjectController extends Controller
{
    public function __construct(
        private RoadmapService $roadmap,
        private SubmissionService $submissions,
    ) {
    }

    /** Detail proyek: info, bahan, yang dipelajari, pratinjau langkah 1. */
    public function show(Request $request, Project $project): View|RedirectResponse
    {
        $user = $request->user();
        $project->loadMissing('hobby');

        $stepIds = RoadmapStep::where('project_id', $project->id)->pluck('id');

        if ($redirect = $this->roadmapRedirect($project, $user)) {
            return $redirect;
        }

        $summary = $this->roadmap->summary($user, $project);

        $learn = $project->learnings;
        if (is_string($learn)) {
            $learn = json_decode($learn, true);
        }

        $first = $summary['steps']->first();

        return view('projects.show', [
            'project' => $project,
            'summary' => $summary,
            'started' => DB::table('user_projects')->where('user_id', $user->id)->where('project_id', $project->id)->exists(),
            'checklist' => app(\App\Services\PackChecklist::class)->forProject($project, $user),
            'learnings' => is_array($learn) ? $learn : [],
            'first' => $first ? $first->load('instructions') : null,
            'shopUrl' => config('hobbynet.shop_search_url'),
        ]);
    }

    /** "Start Project" / "Continue Project". */
    public function start(Request $request, Project $project): RedirectResponse
    {
        if ($redirect = $this->roadmapRedirect($project, $request->user())) {
            return $redirect;
        }

        $this->roadmap->begin($request->user(), $project);
        $summary = $this->roadmap->summary($request->user(), $project);
        $target = $summary['active'] ?? $summary['steps']->first();

        abort_if($target === null, 404);

        if ($summary['is_complete'] && $this->submittedWork($request->user(), $project)) {
            return redirect()->route('projects.celebration', $project->slug);
        }

        return $this->goTo($project, $target);
    }

    /** Satu langkah: instruksi, bahan, tip, checklist. */
    public function step(Request $request, Project $project, string $stepSlug): View|RedirectResponse
    {
        if ($redirect = $this->roadmapRedirect($project, $request->user())) {
            return $redirect;
        }

        [$summary, $step] = $this->locate($request, $project, $stepSlug);

        if ($step->state === 'locked') {
            return $this->lockedRedirect($project);
        }

        if ($step->type === RoadmapStep::TYPE_SHARE) {
            return redirect()->route('projects.submission.show', $project->slug);
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
        $this->abortIfPrimaryRoadmap($project, $request->user());

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
        $this->abortIfPrimaryRoadmap($project, $request->user());

        [$summary, $step] = $this->locate($request, $project, $stepSlug);

        if ($step->state === 'locked') {
            return $this->lockedRedirect($project);
        }

        if ($step->type === RoadmapStep::TYPE_SHARE) {
            return redirect()->route('projects.submission.show', $project->slug);
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

        return $this->goTo($project, $next);
    }

    /** "Look What You Made!": langkah Share, sama seperti roadmap. */
    public function submission(Request $request, Project $project): View|RedirectResponse
    {
        if ($redirect = $this->roadmapRedirect($project, $request->user())) {
            return $redirect;
        }

        [$summary, $shareStep] = $this->shareContext($request, $project);

        if ($shareStep->state === 'locked') {
            return $this->lockedRedirect($project);
        }

        $user = $request->user();
        $submission = ProjectSubmission::with('photos')
            ->where('user_id', $user->id)
            ->where('project_id', $project->id)
            ->first();

        if ($submission?->isSubmitted()) {
            return redirect()->route('projects.celebration', $project->slug);
        }

        return view('projects.submission', [
            'project' => $project,
            'summary' => $summary,
            'step' => $shareStep,
            'submission' => $submission ?? new ProjectSubmission([
                'category' => $project->hobby?->name ?? $project->category,
                'tags' => [],
                'is_showcase' => false,
            ]),
            'maxPhotos' => SubmissionService::MAX_PHOTOS,
        ]);
    }

    /** "Save as Draft" atau "Submit My Project", ditentukan oleh field `action`. */
    public function storeSubmission(SubmissionRequest $request, Project $project): RedirectResponse
    {
        $user = $request->user();
        $this->abortIfPrimaryRoadmap($project, $user);

        [, $shareStep] = $this->shareContext($request, $project);

        if ($shareStep->state === 'locked') {
            return $this->lockedRedirect($project);
        }

        if ($shareStep->state === 'completed' && $this->submittedWork($user, $project)) {
            return redirect()->route('projects.celebration', $project->slug);
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

        return redirect()->route('projects.celebration', $project->slug);
    }

    /** "You did it!": lencana dan saran proyek berikutnya. */
    public function celebration(Request $request, Project $project): View|RedirectResponse
    {
        if ($redirect = $this->roadmapRedirect($project, $request->user())) {
            return $redirect;
        }

        $user = $request->user();
        $submission = $this->submittedWork($user, $project);

        if ($submission === null) {
            return redirect()->route('projects.show', $project->slug);
        }

        $summary = $this->roadmap->summary($user, $project);
        $shareStep = $this->roadmap->shareStep($summary['steps']);

        return view('projects.celebration', [
            'project' => $project,
            'summary' => $summary,
            'submission' => $submission->load('photos'),
            'badge' => $shareStep?->badge,
            'nextProject' => $this->suggestNextProject($project),
        ]);
    }

    /** @return array{0: array, 1: RoadmapStep} */
    private function locate(Request $request, Project $project, string $stepSlug): array
    {
        $summary = $this->roadmap->summary($request->user(), $project);
        $step = $summary['steps']->firstWhere('slug', $stepSlug);

        abort_if($step === null, 404);

        return [$summary, $step];
    }

    /** Proyek roadmap = starter sebuah hobi yang punya langkah, di level mana pun. */
    private function isRoadmapProject(Project $project): bool
    {
        $project->loadMissing('hobby');

        return $project->is_starter
            && $project->hobby
            && RoadmapStep::where('project_id', $project->id)->exists();
    }

    /** Proyek roadmap selalu dibuka di halaman roadmap, bukan sebagai proyek berpemandu. */
    private function roadmapRedirect(Project $project, User $user): ?RedirectResponse
    {
        if (! $this->isRoadmapProject($project)) {
            return null;
        }

        $this->roadmap->openRoadmap($project, $user);

        return redirect()->route('roadmap.show', $project->hobby->slug);
    }

    private function abortIfPrimaryRoadmap(Project $project, User $user): void
    {
        abort_if($this->isRoadmapProject($project), 404);
    }

    /** Buka sebuah langkah; langkah Share diarahkan ke form submission. */
    private function goTo(Project $project, RoadmapStep $step): RedirectResponse
    {
        return $step->type === RoadmapStep::TYPE_SHARE
            ? redirect()->route('projects.submission.show', $project->slug)
            : redirect()->route('projects.steps.show', [$project->slug, $step->slug]);
    }

    /** @return array{0: array, 1: RoadmapStep} */
    private function shareContext(Request $request, Project $project): array
    {
        $summary = $this->roadmap->summary($request->user(), $project);
        $shareStep = $this->roadmap->shareStep($summary['steps']);

        abort_if($shareStep === null, 404);

        return [$summary, $shareStep];
    }

    private function submittedWork(User $user, Project $project): ?ProjectSubmission
    {
        return ProjectSubmission::where('user_id', $user->id)
            ->where('project_id', $project->id)
            ->where('status', ProjectSubmission::STATUS_SUBMITTED)
            ->first();
    }

    /** Proyek lain yang punya langkah: hobi yang sama dulu, lalu yang paling populer. */
    private function suggestNextProject(Project $current): ?Project
    {
        return Project::where('id', '!=', $current->id)
            ->whereIn('id', RoadmapStep::query()->select('project_id'))
            ->orderByRaw('hobby_id = ? desc', [$current->hobby_id])
            ->orderByDesc('learners_count')
            ->first();
    }

    private function lockedRedirect(Project $project): RedirectResponse
    {
        return redirect()
            ->route('projects.show', $project->slug)
            ->with('status', 'That step opens once the one before it is done. One small step at a time.');
    }
}
