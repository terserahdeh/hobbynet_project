<?php

namespace App\Http\Controllers\Onboarding;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreHobbiesRequest;
use App\Models\Hobby;
use App\Models\Project;
use App\Services\RoadmapService;
use Database\Seeders\HobbySeeder;
use Database\Seeders\ProjectSeeder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class HobbyController extends Controller
{
    /** Onboarding Langkah 3: pemilih hobi. Hanya sekali; setelah itu cukup "tambah hobi". */
    public function create(Request $request): View|RedirectResponse
    {
        $user = $request->user();

        if ($user->onboarding_step >= 4) {
            return redirect()->route('home');
        }

        if ($user->onboarding_step < 3) {
            return redirect()->route($user->nextOnboardingRoute());
        }

        if (! Hobby::exists()) {
            (new HobbySeeder)->run();
        }

        return view('onboarding.hobbies', [
            'hobbies'  => Hobby::orderBy('sort')->get(),
            'selected' => array_map('intval', old('hobbies', $user->hobbies()->pluck('hobbies.id')->all())),
        ]);
    }

    /** Simpan pilihan hobi, buat roadmap bawaan, lanjut ke beranda (Langkah 4). */
    public function store(StoreHobbiesRequest $request, RoadmapService $roadmaps): RedirectResponse
    {
        $user = $request->user();

        if ($user->onboarding_step >= 4) {
            return redirect()->route('home');
        }

        if (! Project::exists()) {
            (new ProjectSeeder)->run();
        }

        $user->hobbies()->sync($request->validated('hobbies'));
        $user->update(['onboarding_step' => 4]);
        $roadmaps->ensureDefault($user->fresh());

        return redirect()->route('home');
    }

    /** Pengguna ingin melihat-lihat dulu tanpa memilih hobi. */
    public function skip(Request $request): RedirectResponse
    {
        $user = $request->user();
        $user->update(['onboarding_step' => max($user->onboarding_step, 4)]);

        return redirect()->route('home');
    }

    /** Semua hobi yang tersedia. */
    public function index(Request $request): View
    {
        $hobbies = Hobby::withCount('projects')->orderBy('sort')->get();

        return view('hobbies.index', [
            'hobbies' => $hobbies,
            'chosen'  => $request->user()->hobbies()->pluck('hobbies.id')->all(),
            'general' => Project::whereNull('hobby_id')->orderByDesc('learners_count')->limit(6)->get(),
        ]);
    }

    /** Isi satu hobi: proyeknya dikelompokkan per level, level pengguna di urutan pertama. */
    public function show(Request $request, Hobby $hobby, RoadmapService $roadmaps): View
    {
        $user      = $request->user();
        $userLevel = $roadmaps->levelFor($user);
        $inList    = $user->projectProgress()->pluck('project_id')->all();

        $projects = $hobby->projects()->orderByDesc('is_starter')->orderByDesc('learners_count')->get();

        $levels = collect(Project::LEVELS)
            ->sortBy(fn ($level) => $level === $userLevel ? -1 : Project::levelRank($level))
            ->values();

        $groups = $levels
            ->map(fn ($level) => [
                'level'    => $level,
                'title'    => $level === $userLevel
                    ? 'Untuk levelmu: ' . Project::levelLabel($level)
                    : 'Level ' . Project::levelLabel($level),
                'projects' => $projects->where('level', $level)->values(),
            ])
            ->filter(fn ($group) => $group['projects']->isNotEmpty())
            ->values();

        return view('hobbies.show', [
            'hobby'  => $hobby,
            'chosen' => $user->hobbies()->where('hobbies.id', $hobby->id)->exists(),
            'groups' => $groups,
            'inList' => $inList,
        ]);
    }

    /** Tambah satu hobi ke minat (hanya menambah, tidak mengganti). */
    public function add(Request $request, Hobby $hobby, RoadmapService $roadmaps): RedirectResponse
    {
        $user = $request->user();
        $user->hobbies()->syncWithoutDetaching([$hobby->id]);
        $roadmaps->ensureDefault($user->fresh());

        return redirect()->route('hobbies.show', $hobby)->with('status', $hobby->short_name . ' ditambahkan ke minatmu.');
    }
}
