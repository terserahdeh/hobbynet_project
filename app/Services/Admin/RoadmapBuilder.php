<?php

namespace App\Services\Admin;

use App\Models\Project;
use App\Models\RoadmapStep;
use App\Models\StepChecklistItem;
use App\Models\StepInstruction;
use App\Models\StepMaterial;
use App\Models\UserStepProgress;
use App\Support\UniqueSlug;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

/**
 * Everything the admin does to a roadmap goes through here, so the learner
 * side never sees a half-saved or mis-ordered roadmap.
 */
class RoadmapBuilder
{
    /**
     * Create or update a step together with its materials, instructions and checklist.
     *
     * @param  array<string, mixed>  $data  validated request data
     */
    public function saveStep(Project $project, ?RoadmapStep $step, array $data, ?UploadedFile $image = null): RoadmapStep
    {
        return DB::transaction(function () use ($project, $step, $data, $image) {
            $isNew = $step === null;
            $step ??= new RoadmapStep(['project_id' => $project->id]);

            $isShare = $data['type'] === RoadmapStep::TYPE_SHARE;

            $step->fill([
                'title' => $data['title'],
                'summary' => $data['summary'],
                'objective' => $data['objective'] ?? null,
                'type' => $data['type'],
                'estimated_minutes' => $data['estimated_minutes'],
                'encouragement' => $data['encouragement'] ?? null,
                'badge_id' => $data['badge_id'] ?? null,
                'image_caption' => $data['image_caption'] ?? null,
            ]);

            if ($isNew) {
                $step->slug = UniqueSlug::make($data['title'], 'roadmap_steps', ['project_id' => $project->id]);
                // Parked at the end; normalise() puts it in its final place.
                $step->position = (int) RoadmapStep::where('project_id', $project->id)->max('position') + 1;
            }

            $this->applyImage($step, $image, (bool) ($data['remove_image'] ?? false), $project);
            $step->save();

            if ($isShare) {
                // The share step is a form, not a lesson: it carries no instructions.
                $this->syncRows(StepMaterial::class, $step->id, [], 'name', fn () => []);
                $this->syncRows(StepInstruction::class, $step->id, [], 'body', fn () => []);
                $this->syncRows(StepChecklistItem::class, $step->id, [], 'label', fn () => []);
            } else {
                $this->syncRows(StepMaterial::class, $step->id, $data['materials'] ?? [], 'name', fn (array $r) => [
                    'kind' => $r['kind'] ?? 'tool',
                    'name' => trim($r['name']),
                    'spec' => filled($r['spec'] ?? null) ? trim($r['spec']) : null,
                    'is_optional' => (bool) ($r['is_optional'] ?? false),
                ]);

                $this->syncRows(StepInstruction::class, $step->id, $data['instructions'] ?? [], 'body', fn (array $r) => [
                    'body' => trim($r['body']),
                ]);

                $this->syncRows(StepChecklistItem::class, $step->id, $data['checklist'] ?? [], 'label', fn (array $r) => [
                    'label' => trim($r['label']),
                ]);
            }

            $this->normalise($project);
            $this->recalculateProgress($project);

            return $step->refresh();
        });
    }

    public function deleteStep(RoadmapStep $step): void
    {
        DB::transaction(function () use ($step) {
            $project = Project::findOrFail($step->project_id);

            if ($step->image) {
                Storage::disk('public')->delete($step->image);
            }

            $step->delete(); // materials, instructions, checklist and progress cascade

            $this->normalise($project);
            $this->recalculateProgress($project);
        });
    }

    /** Move a step one place earlier or later. The share step stays last. */
    public function moveStep(RoadmapStep $step, string $direction): void
    {
        if ($step->isShare()) {
            return;
        }

        DB::transaction(function () use ($step, $direction) {
            $ids = $this->orderedSteps($step->project_id)->pluck('id')->all();
            $from = array_search($step->id, $ids, true);

            if ($from === false) {
                return;
            }

            $to = $direction === 'up' ? $from - 1 : $from + 1;

            // Never swap past the share step at the end.
            $lastMovable = count($ids) - 1 - ($this->hasShareStep($step->project_id) ? 1 : 0);

            if ($to < 0 || $to > $lastMovable) {
                return;
            }

            [$ids[$from], $ids[$to]] = [$ids[$to], $ids[$from]];
            $this->applyOrder($step->project_id, $ids);
        });
    }

    /** Re-number positions 1..n with the share step pinned last. */
    public function normalise(Project $project): void
    {
        $this->applyOrder($project->id, $this->orderedSteps($project->id)->pluck('id')->all());
    }

    /**
     * Why a roadmap can't be shown to learners yet. Empty means it's ready.
     *
     * @return string[]
     */
    public function problems(Project $project): array
    {
        $problems = [];

        if (! $project->is_starter || ! $project->hobby_id) {
            $problems[] = 'Make this the starter project of a hobby (Project settings).';
        }

        $steps = RoadmapStep::where('project_id', $project->id)
            ->withCount(['instructions'])
            ->orderBy('position')
            ->get();

        $lessons = $steps->reject->isShare();

        if ($lessons->isEmpty()) {
            $problems[] = 'Add at least one lesson step.';
        }

        if (! $steps->contains->isShare()) {
            $problems[] = 'Add the final “share” step where learners upload what they made.';
        }

        foreach ($lessons as $s) {
            if ($s->instructions_count === 0) {
                $problems[] = "“{$s->title}” has no instructions.";
            }
        }

        return $problems;
    }

    /** Hide the roadmap from learners if edits have left it incomplete. Returns true if it was hidden. */
    public function unpublishIfBroken(Project $project): bool
    {
        if ($project->roadmap_published && $this->problems($project) !== []) {
            $project->forceFill(['roadmap_published' => false])->save();

            return true;
        }

        return false;
    }

    /** Refresh every learner's progress % after steps were added or removed. */
    public function recalculateProgress(Project $project): void
    {
        $stepIds = RoadmapStep::where('project_id', $project->id)->pluck('id');
        $total = $stepIds->count();

        $done = UserStepProgress::whereIn('roadmap_step_id', $stepIds)
            ->selectRaw('user_id, count(*) as finished')
            ->groupBy('user_id')
            ->pluck('finished', 'user_id');

        DB::table('user_projects')
            ->where('project_id', $project->id)
            ->chunkById(500, function (Collection $rows) use ($done, $total) {
                foreach ($rows as $row) {
                    $percent = $total > 0 ? (int) min(100, round(($done[$row->user_id] ?? 0) / $total * 100)) : 0;

                    if ($percent !== (int) $row->progress_percent) {
                        DB::table('user_projects')->where('id', $row->id)->update([
                            'progress_percent' => $percent,
                            'updated_at' => now(),
                        ]);
                    }
                }
            });
    }

    /** How many learners have started, and how many finished, a roadmap. */
    public function learnerCounts(Project $project): array
    {
        $stepIds = RoadmapStep::where('project_id', $project->id)->pluck('id');
        $total = $stepIds->count();

        $perUser = UserStepProgress::whereIn('roadmap_step_id', $stepIds)
            ->selectRaw('user_id, count(*) as finished')
            ->groupBy('user_id')
            ->pluck('finished');

        return [
            'started' => $perUser->count(),
            'finished' => $total > 0 ? $perUser->filter(fn ($n) => $n >= $total)->count() : 0,
        ];
    }

    // ---------------------------------------------------------------- helpers

    /** @return Collection<int, RoadmapStep> lessons first, share step last */
    private function orderedSteps(int $projectId): Collection
    {
        return RoadmapStep::where('project_id', $projectId)
            ->orderByRaw("case when type = 'share' then 1 else 0 end")
            ->orderBy('position')
            ->orderBy('id')
            ->get(['id', 'type']);
    }

    private function hasShareStep(int $projectId): bool
    {
        return RoadmapStep::where('project_id', $projectId)->where('type', RoadmapStep::TYPE_SHARE)->exists();
    }

    /**
     * Write positions 1..n. Positions are unique per project, so rows are first
     * moved out of the way to avoid clashing with each other mid-update.
     *
     * @param  int[]  $ids
     */
    private function applyOrder(int $projectId, array $ids): void
    {
        RoadmapStep::where('project_id', $projectId)->increment('position', 100);

        foreach (array_values($ids) as $i => $id) {
            RoadmapStep::where('id', $id)->update(['position' => $i + 1]);
        }
    }

    /**
     * Make a step's child rows match what the form sent. Rows are matched by id,
     * so existing rows keep their id (and learners keep their ticked checklist items).
     *
     * @param  class-string  $model
     * @param  array<int, array<string, mixed>>  $rows
     */
    private function syncRows(string $model, int $stepId, array $rows, string $requiredKey, callable $map): void
    {
        $existing = $model::where('roadmap_step_id', $stepId)->get()->keyBy('id');
        $keep = [];
        $position = 0;

        foreach ($rows as $row) {
            if (blank($row[$requiredKey] ?? null)) {
                continue;
            }

            $attributes = $map($row) + ['position' => ++$position];
            $id = isset($row['id']) ? (int) $row['id'] : 0;

            if ($id && $existing->has($id)) {
                $existing[$id]->update($attributes);
                $keep[] = $id;
            } else {
                $keep[] = $model::create($attributes + ['roadmap_step_id' => $stepId])->id;
            }
        }

        $model::where('roadmap_step_id', $stepId)->whereNotIn('id', $keep)->delete();
    }

    private function applyImage(RoadmapStep $step, ?UploadedFile $image, bool $remove, Project $project): void
    {
        if (($remove || $image) && $step->image) {
            $step->image = null;
        }

        if ($image) {
            $step->image = file_get_contents($image->getRealPath());
        }
    }
}
