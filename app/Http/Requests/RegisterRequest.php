<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;

class RegisterRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name'     => ['required', 'string', 'max:100'],
            'email'    => ['required', 'string', 'lowercase', 'email:rfc', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', Password::min(8)->letters()->numbers()],
        ];
    }

    public function attributes(): array
    {
        return [
            'name'     => 'nama lengkap',
            'email'    => 'alamat email',
            'password' => 'kata sandi',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required'     => 'Tulis nama lengkapmu dulu ya.',
            'email.required'    => 'Tulis alamat emailmu dulu ya.',
            'email.email'       => 'Format email belum benar. Contoh: alex@example.com.',
            'email.unique'      => 'Email ini sudah terdaftar. Coba masuk dengan email ini.',
            'password.required' => 'Buat kata sandi dulu ya.',
            'password.min'      => 'Kata sandi minimal 8 karakter.',
            'password.letters'  => 'Kata sandi perlu memuat huruf.',
            'password.numbers'  => 'Kata sandi perlu memuat angka.',
        ];
    }
}
