<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;
use Throwable;

/**
 * Perlu: composer require laravel/socialite
 * Apple memerlukan paket tambahan: composer require socialiteproviders/apple
 */
class SocialAuthController extends Controller
{
    public function redirect(string $provider)
    {
        return Socialite::driver($provider)->redirect();
    }

    public function callback(string $provider): RedirectResponse
    {
        try {
            $social = Socialite::driver($provider)->user();
        } catch (Throwable) {
            return redirect()->route('onboarding.account')
                ->withErrors(['email' => 'Masuk dengan ' . ucfirst($provider) . ' belum berhasil. Coba lagi.']);
        }

        if (! $social->getEmail()) {
            return redirect()->route('onboarding.account')
                ->withErrors(['email' => 'Akun ' . ucfirst($provider) . ' tidak membagikan email. Daftar dengan email saja.']);
        }

        $user = User::firstOrCreate(
            ['email' => $social->getEmail()],
            [
                'name'              => $social->getName() ?: Str::before($social->getEmail(), '@'),
                'password'          => Str::random(40), // dihash otomatis oleh cast 'hashed'
                'email_verified_at' => now(),
                'onboarding_step'   => 2,
            ]
        );

        Auth::login($user, true);
        session()->regenerate();

        return redirect()->route('home');
    }
}
