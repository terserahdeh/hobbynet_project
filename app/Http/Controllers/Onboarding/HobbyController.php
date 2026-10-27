<?php

namespace App\Http\Controllers\Onboarding;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreHobbiesRequest;
use App\Models\Hobby;
use Database\Seeders\HobbySeeder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class HobbyController extends Controller
{
    /** Onboarding Langkah 3: pemilih hobi (juga dipakai untuk mengubah minat dari beranda). */
    public function create(Request $request): View|RedirectResponse
    {
        $user = $request->user();

        // Jangan loncat: personalisasi dulu.
        if ($user->onboarding_step < 3) {
            return redirect()->route($user->nextOnboardingRoute());
        }

        // Jaring pengaman: isi daftar hobi jika seeder belum dijalankan.
        if (! Hobby::exists()) {
            (new HobbySeeder)->run();
        }

        return view('onboarding.hobbies', [
            'hobbies'  => Hobby::orderBy('sort')->get(),
            'selected' => array_map('intval', old('hobbies', $user->hobbies()->pluck('hobbies.id')->all())),
        ]);
    }

    /** Simpan pilihan hobi. Langkah 4 = langsung ke beranda. */
    public function store(StoreHobbiesRequest $request): RedirectResponse
    {
        $user = $request->user();

        $user->hobbies()->sync($request->validated('hobbies'));
        $user->update(['onboarding_step' => max($user->onboarding_step, 4)]);

        return redirect()->route('home');
    }

    /** Pengguna ingin melihat-lihat dulu tanpa memilih hobi. */
    public function skip(Request $request): RedirectResponse
    {
        $user = $request->user();
        $user->update(['onboarding_step' => max($user->onboarding_step, 4)]);

        return redirect()->route('home');
    }
}
