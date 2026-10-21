<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreHobbiesRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'hobbies'   => ['required', 'array', 'min:1'],
            'hobbies.*' => ['integer', 'distinct', 'exists:hobbies,id'],
        ];
    }

    public function messages(): array
    {
        return [
            'hobbies.required' => 'Pilih minimal satu hobi, atau lewati dulu.',
            'hobbies.min'      => 'Pilih minimal satu hobi, atau lewati dulu.',
            'hobbies.*.exists' => 'Ada hobi yang tidak dikenali. Muat ulang halaman lalu coba lagi.',
        ];
    }
}
