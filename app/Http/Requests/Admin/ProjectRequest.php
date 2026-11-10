<?php

namespace App\Http\Requests\Admin;

use App\Models\Project;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class ProjectRequest extends FormRequest
{
    public const LEVELS = ['beginner', 'intermediate', 'advanced'];
    public const COLORS = ['pink', 'green', 'blue'];

    public function authorize(): bool
    {
        return (bool) $this->user()?->is_admin;
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'is_starter' => $this->boolean('is_starter'),
            'is_popular' => $this->boolean('is_popular'),
            'hobby_id' => $this->filled('hobby_id') ? $this->input('hobby_id') : null,
        ]);
    }

    public function rules(): array
    {
        return [
            'hobby_id' => ['nullable', 'integer', 'exists:hobbies,id'],
            'title' => ['required', 'string', 'max:255'],
            'category' => ['required', 'string', 'max:255'],
            'summary' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:5000'],
            'level' => ['required', Rule::in(self::LEVELS)],
            'duration_minutes' => ['required', 'integer', 'between:1,10080'],
            'image' => ['nullable', 'string', 'max:255'],
            'icon' => ['nullable', 'string', 'max:30'],
            'color' => ['required', Rule::in(self::COLORS)],
            'is_starter' => ['boolean'],
            'is_popular' => ['boolean'],
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $v) {
            if (! $this->boolean('is_starter')) {
                return;
            }

            if (! $this->input('hobby_id')) {
                $v->errors()->add('is_starter', 'Pick a hobby first. A starter project belongs to one hobby.');

                return;
            }

            /** @var Project|null $current */
            $current = $this->route('project');

            $clash = Project::where('hobby_id', $this->input('hobby_id'))
                ->where('is_starter', true)
                ->when($current, fn ($q) => $q->where('id', '!=', $current->id))
                ->first();

            if ($clash) {
                $v->errors()->add('is_starter', "“{$clash->title}” is already the starter project for this hobby. Turn that one off first.");
            }
        });
    }

    public function attributes(): array
    {
        return [
            'hobby_id' => 'hobby',
            'duration_minutes' => 'duration',
        ];
    }
}
