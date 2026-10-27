<?php

namespace App\Services;

use App\Models\Project;
use App\Models\ProjectSubmission;
use App\Models\RoadmapStep;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;

class SubmissionService
{
    public const MAX_PHOTOS = 5;

    public function __construct(private RoadmapService $roadmap)
    {
    }

    /**
     * Save a draft, or submit the project (which also completes the share step).
     *
     * @param  array<string, mixed>  $data        title, reflection, category, tags, is_showcase
     * @param  UploadedFile[]        $newPhotos
     * @param  int[]                 $removePhotoIds
     * @return array{submission: ProjectSubmission, badges: Collection}
     */
    public function save(
        User $user,
        Project $project,
        RoadmapStep $shareStep,
        array $data,
        array $newPhotos,
        array $removePhotoIds,
        bool $submit,
    ): array {
        return DB::transaction(function () use ($user, $project, $shareStep, $data, $newPhotos, $removePhotoIds, $submit) {
            $submission = ProjectSubmission::firstOrNew([
                'user_id' => $user->id,
                'project_id' => $project->id,
            ]);

            $submission->fill([
                'title' => $data['title'] ?? null,
                'reflection' => $data['reflection'] ?? null,
                'category' => $data['category'] ?? null,
                'tags' => $data['tags'] ?? [],
                'is_showcase' => (bool) ($data['is_showcase'] ?? false),
            ]);

            if (! $submission->exists) {
                $submission->status = ProjectSubmission::STATUS_DRAFT;
            }
            $submission->save();

            $toRemove = $submission->photos()->whereIn('id', $removePhotoIds)->get();
            $remaining = $submission->photos()->count() - $toRemove->count();

            if ($remaining + count($newPhotos) > self::MAX_PHOTOS) {
                throw ValidationException::withMessages([
                    'photos' => 'You can add up to ' . self::MAX_PHOTOS . ' photos.',
                ]);
            }

            if ($submit && $remaining + count($newPhotos) < 1) {
                throw ValidationException::withMessages([
                    'photos' => 'Add at least one photo of what you made.',
                ]);
            }

            foreach ($toRemove as $photo) {
                Storage::disk('public')->delete($photo->path);
                $photo->delete();
            }

            $position = (int) $submission->photos()->max('position');
            foreach ($newPhotos as $file) {
                $submission->photos()->create([
                    'path' => $file->store("submissions/{$user->id}/{$project->id}", 'public'),
                    'position' => ++$position,
                ]);
            }

            $badges = collect();

            if ($submit) {
                if (! $submission->isSubmitted()) {
                    $submission->forceFill([
                        'status' => ProjectSubmission::STATUS_SUBMITTED,
                        'submitted_at' => now(),
                    ])->save();
                }

                $badges = $this->roadmap->completeStep($user, $shareStep);
            }

            return [
                'submission' => $submission->load('photos'),
                'badges' => $badges,
            ];
        });
    }
}
