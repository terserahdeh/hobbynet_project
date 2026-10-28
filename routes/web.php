<?php

use App\Http\Controllers\AiCoachController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\SocialAuthController;
use App\Http\Controllers\BookmarkController;          // BARU
use App\Http\Controllers\PremiumController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\Onboarding\HobbyController;
use App\Http\Controllers\Onboarding\PersonalizeController;
use App\Http\Controllers\Roadmap\RoadmapController;
use App\Http\Controllers\Roadmap\RoadmapStepController;
use App\Http\Controllers\Roadmap\RoadmapSubmissionController;
use App\Http\Controllers\ProfileController;           // BARU
use App\Http\Controllers\CourseController;
use App\Http\Controllers\WorkController;
use App\Http\Controllers\StarterPackController;       // BARU
use App\Models\Project;
use Illuminate\Support\Facades\Route;

// Selalu mulai dari landing page, termasuk untuk pengguna yang sudah login
// (di landing mereka melihat tombol "Buka Beranda").
Route::get('/', fn () => view('LandingPage'))->name('landing');

// Halaman publik: harga HobbyNet Plus
Route::get('/plus', [PremiumController::class, 'index'])->name('plus');

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

    // Jelajahi + pencarian + detail starter pack  (MENGGANTIKAN placeholder 'explore' dan 'search')
    Route::get('/explore', [StarterPackController::class, 'index'])->name('explore');
    Route::get('/search', [StarterPackController::class, 'index'])->name('search');
    Route::get('/explore/{starterPack}', [StarterPackController::class, 'show'])->name('packs.show');

    // Tersimpan  (MENGGANTIKAN placeholder 'bookmarks')
    Route::get('/bookmarks', [BookmarkController::class, 'index'])->name('bookmarks');
    Route::post('/bookmarks/{starterPack}', [BookmarkController::class, 'toggle'])->name('bookmarks.toggle');

    // Profil
    Route::get('/profile', [ProfileController::class, 'show'])->name('profile');
    Route::get('/profile/edit', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::get('/explore/{starterPack}/learn', [CourseController::class, 'show'])->name('courses.show');
    Route::post('/explore/{starterPack}/learn/{lesson}/complete', [CourseController::class, 'complete'])->name('courses.complete');

    Route::get('/explore/{starterPack}/submit', [CourseController::class, 'submission'])->name('courses.submission');
    Route::post('/explore/{starterPack}/submit', [CourseController::class, 'saveSubmission'])->name('courses.submission.save');
    Route::get('/my-works/course/{submission}', [CourseController::class, 'work'])->name('courses.work.show');
    Route::get('/my-works/course/{submission}/photos/{index}',[CourseController::class, 'photo'])->whereNumber('index')->name('courses.work.photo');
    Route::get('/my-works/roadmap/{submission}', [WorkController::class, 'show'])->name('roadmap.work.show');

    // Coach AI + langganan Plus
    Route::post('/plus/subscribe', [PremiumController::class, 'subscribe'])->name('plus.subscribe');
    Route::post('/plus/cancel', [PremiumController::class, 'cancel'])->name('plus.cancel');
    // HobbyNet Plus


    // Halaman proyek (masih placeholder)
    Route::get('/projects/{project:slug}', fn (Project $project) => view('soon', ['title' => $project->title]))
        ->name('projects.show');

    Route::post('/logout', [LoginController::class, 'destroy'])->name('logout');
});

// Submission photos
Route::middleware('auth')->get(
    '/submission-photos/{photo}',
    [RoadmapSubmissionController::class, 'showPhoto']
)->name('submission-photos.show');

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