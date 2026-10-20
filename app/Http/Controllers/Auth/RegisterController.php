<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\RegisterRequest;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class RegisterController extends Controller
{
    /** Onboarding Step 1: form pembuatan akun. */
    public function create(): View
    {
        return view('onboarding.account');
    }

    /** Simpan akun, login otomatis, lanjut ke Step 2 (pemilih hobi). */
    public function store(RegisterRequest $request): RedirectResponse
    {
        $user = User::create([
            'name'            => $request->validated('name'),
            'email'           => $request->validated('email'),
            'password'        => Hash::make($request->validated('password')),
            'onboarding_step' => 2,
        ]);

        Auth::login($user);
        $request->session()->regenerate();

        return redirect()->route('onboarding.personalize');
    }
}
