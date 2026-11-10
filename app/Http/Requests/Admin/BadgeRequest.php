<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class BadgeRequest extends FormRequest
{
    public const ICONS = ['default', 'compass', 'stitch', 'sparkle', 'check'];
    public const COLORS = ['pink', 'green', 'blue'];

    public function authorize(): bool
    {
        return (bool) $this->user()?->is_admin;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string', 'max:255'],
            'icon' => ['required', Rule::in(self::ICONS)],
            'color' => ['required', Rule::in(self::COLORS)],
        ];
    }
}
