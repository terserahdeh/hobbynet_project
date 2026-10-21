<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StorePersonalizationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'experience_level' => ['required', Rule::in(['beginner', 'some', 'returning'])],
            'tools'            => ['nullable', 'array'],
            'tools.*'          => [Rule::in(['paintbrushes', 'yarn_hook', 'camera', 'none'])],
            'budget'           => ['nullable', Rule::in(['budget', 'standard', 'premium'])],
        ];
    }

    public function messages(): array
    {
        return [
            'experience_level.required' => 'Pilih tingkat pengalamanmu dulu ya.',
            'experience_level.in'       => 'Pilihan pengalaman tidak dikenali. Muat ulang halaman lalu coba lagi.',
            'tools.*.in'                => 'Ada alat yang tidak dikenali. Muat ulang halaman lalu coba lagi.',
            'budget.in'                 => 'Pilihan anggaran tidak dikenali. Muat ulang halaman lalu coba lagi.',
        ];
    }
}
