<?php

namespace App\Http\Requests\Admin;

use App\Models\RoadmapStep;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class RoadmapStepRequest extends FormRequest
{
    public const TYPES = [
        'materials' => 'Materials',
        'lesson' => 'Lesson',
        'practice' => 'Practice',
        'project' => 'Project',
        'share' => 'Share (final step)',
    ];

    public function authorize(): bool
    {
        return (bool) $this->user()?->is_admin;
    }

    /** The roadmap this step belongs to, for both create and edit. */
    public function projectId(): int
    {
        $step = $this->route('step');

        return $step instanceof RoadmapStep ? $step->project_id : (int) $this->route('project')->id;
    }

    protected function prepareForValidation(): void
    {
        $this->merge(['remove_image' => $this->boolean('remove_image')]);
    }

    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'summary' => ['required', 'string', 'max:255'],
            'objective' => ['nullable', 'string', 'max:255'],
            'type' => ['required', Rule::in(array_keys(self::TYPES))],
            'estimated_minutes' => ['required', 'integer', 'between:1,600'],
            'encouragement' => ['nullable', 'string', 'max:1000'],
            'badge_id' => ['nullable', 'integer', 'exists:badges,id'],

            'image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
            'image_caption' => ['nullable', 'string', 'max:120'],
            'remove_image' => ['boolean'],

            'materials' => ['nullable', 'array', 'max:20'],
            'materials.*.id' => ['nullable', 'integer'],
            'materials.*.kind' => ['nullable', Rule::in(['tool', 'material'])],
            'materials.*.name' => ['nullable', 'string', 'max:255'],
            'materials.*.spec' => ['nullable', 'string', 'max:255'],
            'materials.*.is_optional' => ['nullable', 'boolean'],

            'instructions' => ['nullable', 'array', 'max:30'],
            'instructions.*.id' => ['nullable', 'integer'],
            'instructions.*.body' => ['nullable', 'string', 'max:2000'],

            'checklist' => ['nullable', 'array', 'max:20'],
            'checklist.*.id' => ['nullable', 'integer'],
            'checklist.*.label' => ['nullable', 'string', 'max:255'],
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $v) {
            $type = $this->input('type');
            $current = $this->route('step');

            if ($type === RoadmapStep::TYPE_SHARE) {
                $clash = RoadmapStep::where('project_id', $this->projectId())
                    ->where('type', RoadmapStep::TYPE_SHARE)
                    ->when($current instanceof RoadmapStep, fn ($q) => $q->where('id', '!=', $current->id))
                    ->first();

                if ($clash) {
                    $v->errors()->add('type', "“{$clash->title}” is already this roadmap's final share step. A roadmap has only one.");
                }

                return;
            }

            $hasInstruction = collect($this->input('instructions', []))
                ->contains(fn ($row) => filled($row['body'] ?? null));

            if (! $hasInstruction) {
                $v->errors()->add('instructions', 'Add at least one instruction so learners know what to do.');
            }
        });
    }

    public function messages(): array
    {
        return [
            'image.image' => 'That file isn\'t a picture. Use a JPG, PNG or WebP.',
            'image.max' => 'That picture is over 5 MB. Try a smaller one.',
        ];
    }
}
