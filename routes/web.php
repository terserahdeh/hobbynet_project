<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\SocialAuthController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\Onboarding\HobbyController;
use App\Http\Controllers\Onboarding\PersonalizeController;
use App\Models\Project;
use Illuminate\Support\Facades\Route;

Route::get('/', fn () => auth()->check()
    ? redirect()->route('home')
    : view('LandingPage'))->name('landing');

// Tamu (belum login)
Route::middleware('guest')->group(function () {
    // Langkah 1: akun
    Route::get('/onboarding/account', [RegisterController::class, 'create'])->name('onboarding.account');
    Route::post('/onboarding/account', [RegisterController::class, 'store'])
        ->middleware('throttle:10,1')
        ->name('onboarding.account.store');

    Route::get('/login', [LoginController::class, 'create'])->name('login');
    Route::post('/login', [LoginController::class, 'store'])
        ->middleware('throttle:10,1')
        ->name('login.store');

    // Login sosial (Google / Apple), butuh laravel/socialite
    Route::get('/auth/{provider}/redirect', [SocialAuthController::class, 'redirect'])
        ->whereIn('provider', ['google', 'apple'])->name('social.redirect');
    Route::get('/auth/{provider}/callback', [SocialAuthController::class, 'callback'])
        ->whereIn('provider', ['google', 'apple'])->name('social.callback');
});

// Pengguna sudah login
Route::middleware('auth')->group(function () {
    // Langkah 2: personalisasi
    Route::get('/onboarding/personalize', [PersonalizeController::class, 'create'])->name('onboarding.personalize');
    Route::post('/onboarding/personalize', [PersonalizeController::class, 'store'])->name('onboarding.personalize.store');
    Route::post('/onboarding/personalize/skip', [PersonalizeController::class, 'skip'])->name('onboarding.personalize.skip');

    // Langkah 3: pemilih hobi (Langkah 4 = redirect ke beranda)
    Route::get('/onboarding/hobbies', [HobbyController::class, 'create'])->name('onboarding.hobbies');
    Route::post('/onboarding/hobbies', [HobbyController::class, 'store'])->name('onboarding.hobbies.store');
    Route::post('/onboarding/hobbies/skip', [HobbyController::class, 'skip'])->name('onboarding.hobbies.skip');

    // Beranda
    Route::get('/home', HomeController::class)->name('home');

    // Halaman yang belum dibuat (placeholder)
    Route::view('/explore', 'soon', ['title' => 'Jelajahi'])->name('explore');
    Route::view('/bookmarks', 'soon', ['title' => 'Tersimpan'])->name('bookmarks');
    Route::view('/search', 'soon', ['title' => 'Pencarian'])->name('search');
    Route::get('/projects/{project:slug}', fn (Project $project) => view('soon', ['title' => $project->title]))
        ->name('projects.show');

    Route::post('/logout', [LoginController::class, 'destroy'])->name('logout');
});
