<?php

namespace App\Http\Requests;

use App\Services\SubmissionService;
use Illuminate\Foundation\Http\FormRequest;

class SubmissionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $tags = array_values(array_filter(
            $this->input('tags', []),
            fn ($tag) => filled($tag)
        ));
        $this->merge([
            'is_showcase' => $this->boolean('is_showcase'),
        ]);
    }

    public function rules(): array
    {
        return [
            'action' => ['required', 'in:draft,submit'],
            'title' => ['required_if:action,submit', 'nullable', 'string', 'max:120'],
            'reflection' => ['nullable', 'string', 'max:2000'],
            'category' => ['nullable', 'string', 'max:50'],
            'tags' => ['nullable', 'array', 'max:5'],
            'tags.*' => ['nullable','string', 'max:30'],
            'is_showcase' => ['boolean'],
            'photos' => ['nullable', 'array', 'max:' . SubmissionService::MAX_PHOTOS],
            'photos.*' => ['image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
            'remove_photo_ids' => ['nullable', 'array'],
            'remove_photo_ids.*' => ['integer'],
        ];
    }

    public function messages(): array
    {
        return [
            'title.required_if' => 'Give your creation a name so you can find it later.',
            'photos.max' => 'You can add up to ' . SubmissionService::MAX_PHOTOS . ' photos.',
            'photos.*.image' => 'That file doesn\'t look like a photo. Try a JPG, PNG or WebP.',
            'photos.*.max' => 'That photo is a little too large. Try one under 5 MB.',
        ];
    }
}
