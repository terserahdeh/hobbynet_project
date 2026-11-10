<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class HobbyRequest extends FormRequest
{
    public const COLORS = ['pink', 'green', 'blue'];

    public function authorize(): bool
    {
        return (bool) $this->user()?->is_admin;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'tagline' => ['required', 'string', 'max:255'],
            'first_project' => ['required', 'string', 'max:255'],
            'color' => ['required', Rule::in(self::COLORS)],
            'sort' => ['nullable', 'integer', 'between:0,65535'],
        ];
    }

    public function attributes(): array
    {
        return ['first_project' => 'first project'];
    }
}
