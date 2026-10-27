<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\SocialAuthController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\Onboarding\HobbyController;
use App\Http\Controllers\Onboarding\PersonalizeController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\Roadmap\RoadmapController;
use App\Http\Controllers\Roadmap\RoadmapStepController;
use App\Http\Controllers\Roadmap\RoadmapSubmissionController;
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

    // Login sosial (Google), butuh laravel/socialite
    Route::get('/auth/{provider}/redirect', [SocialAuthController::class, 'redirect'])
        ->whereIn('provider', ['google'])->name('social.redirect');
    Route::get('/auth/{provider}/callback', [SocialAuthController::class, 'callback'])
        ->whereIn('provider', ['google'])->name('social.callback');
});

// Pengguna sudah login
Route::middleware('auth')->group(function () {
    // Langkah 2: personalisasi
    Route::get('/onboarding/personalize', [PersonalizeController::class, 'create'])->name('onboarding.personalize');
    Route::post('/onboarding/personalize', [PersonalizeController::class, 'store'])->name('onboarding.personalize.store');
    Route::post('/onboarding/personalize/skip', [PersonalizeController::class, 'skip'])->name('onboarding.personalize.skip');

    // Langkah 3: pemilih hobi (hanya sekali). Langkah 4 = redirect ke beranda.
    Route::get('/onboarding/hobbies', [HobbyController::class, 'create'])->name('onboarding.hobbies');
    Route::post('/onboarding/hobbies', [HobbyController::class, 'store'])->name('onboarding.hobbies.store');
    Route::post('/onboarding/hobbies/skip', [HobbyController::class, 'skip'])->name('onboarding.hobbies.skip');

    // Beranda
    Route::get('/home', HomeController::class)->name('home');

    // Jelajahi hobi (hobi baru hanya bisa ditambah, tidak diganti)
    Route::get('/explore', [HobbyController::class, 'index'])->name('explore');
    Route::get('/hobbies/{hobby:slug}', [HobbyController::class, 'show'])->name('hobbies.show');
    Route::post('/hobbies/{hobby:slug}/add', [HobbyController::class, 'add'])->name('hobbies.add');

    // Proyek: detail, daftar (to-do), dan langkah roadmap
    Route::get('/projects/{project:slug}', [ProjectController::class, 'show'])->name('projects.show');
    Route::post('/projects/{project:slug}/add', [ProjectController::class, 'add'])->name('projects.add');
    Route::post('/projects/{project:slug}/roadmap', [ProjectController::class, 'makeRoadmap'])->name('projects.roadmap');
    Route::delete('/projects/{project:slug}', [ProjectController::class, 'destroy'])->name('projects.destroy');
    Route::get('/projects/{project:slug}/steps/{position}', [ProjectController::class, 'step'])
        ->whereNumber('position')->name('projects.step');
    Route::post('/projects/{project:slug}/steps/{position}/complete', [ProjectController::class, 'completeStep'])
        ->whereNumber('position')->name('projects.step.complete');

    // Halaman yang belum dibuat (placeholder)
    Route::view('/bookmarks', 'soon', ['title' => 'Tersimpan'])->name('bookmarks');
    Route::view('/search', 'soon', ['title' => 'Pencarian'])->name('search');

    Route::post('/logout', [LoginController::class, 'destroy'])->name('logout');
});

// guided roadmap
Route::middleware('auth')->prefix('roadmap')->name('roadmap.')->group(function () {
    Route::get('/', [RoadmapController::class, 'index'])->name('index');
    // Must stay above {hobby:slug}. Used by the home screen.
    Route::get('project/{project:slug}', [RoadmapController::class, 'project'])->name('project');
    Route::get('{hobby:slug}', [RoadmapController::class, 'show'])->name('show');

    Route::prefix('{hobby:slug}')->group(function () {
        // Checkpoints
        Route::get('steps/{stepSlug}', [RoadmapStepController::class, 'show'])->name('steps.show');
        Route::post('steps/{stepSlug}/complete', [RoadmapStepController::class, 'complete'])->name('steps.complete');
        Route::patch('steps/{stepSlug}/checklist/{item}', [RoadmapStepController::class, 'toggleChecklist'])->name('steps.checklist');

        // Share your creation, then celebrate
        Route::get('submission', [RoadmapSubmissionController::class, 'show'])->name('submission.show');
        Route::post('submission', [RoadmapSubmissionController::class, 'store'])->name('submission.store');
        Route::get('celebration', [RoadmapSubmissionController::class, 'celebration'])->name('celebration');
    });
});