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
    public const MAX_PHOTOS = 1;

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

            $oldPhotos = $submission->photos()->get();
            $newFile = $newPhotos[0] ?? null;
            if (count($newPhotos) > 1) {
                throw ValidationException::withMessages(['photos' => 'Maksimal satu foto untuk setiap karya.']);
            }
            if ($submit && ! $newFile && $oldPhotos->isEmpty()) {
                throw ValidationException::withMessages(['photos' => 'Unggah satu foto hasil karya.']);
            }
            if ($newFile) {
                $imageData = file_get_contents($newFile->getRealPath());

                if ($imageData === false) {
                    throw new \RuntimeException('Gagal membaca file gambar.');
                }

                $submission->photos()->delete();

                $submission->photos()->create([
                    'path' => '',
                    'position' => 1,
                    'image_data' => $imageData,
                    'mime_type' => $newFile->getMimeType(),
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
