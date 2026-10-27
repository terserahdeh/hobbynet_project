<?php

namespace App\Http\Controllers;

use App\Models\Hobby;
use App\Models\Project;
use App\Models\User;
use App\Services\RoadmapService;
use App\Support\TipOfTheDay;
use Database\Seeders\ProjectSeeder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Illuminate\View\View;

class HomeController extends Controller
{
    public function __invoke(Request $request, RoadmapService $roadmaps): View|RedirectResponse
    {
        $user = $request->user();

        // Onboarding belum selesai: arahkan ke langkah yang tertinggal.
        if ($user->onboarding_step < 4) {
            return redirect()->route($user->nextOnboardingRoute());
        }

        // Jaring pengaman: isi katalog proyek jika seeder belum dijalankan.
        if (! Project::exists()) {
            (new ProjectSeeder)->run();
        }

        $roadmaps->ensureDefault($user);

        $hobbies  = $user->hobbies()->orderBy('sort')->get();
        $chosen   = $hobbies->pluck('id');
        $level    = $roadmaps->levelFor($user);

        $rows = $user->projectProgress()
            ->with('project.hobby')
            ->orderByDesc('is_roadmap')
            ->orderByDesc('updated_at')
            ->get();

        // Data lama tanpa roadmap utama: jadikan proyek terbaru sebagai roadmap.
        $active = $rows->firstWhere('is_roadmap', true);
        if (! $active && $rows->isNotEmpty()) {
            $active = $rows->first();
            $roadmaps->activate($user, $active->project);
            $active->is_roadmap = true;
        }

        $inList = $rows->pluck('project_id');

        return view('home', [
            'firstName'    => Str::before($user->name, ' '),
            'hobbies'      => $hobbies,
            'levelLabel'   => Project::levelLabel($level),
            'roadmap'      => $active ? $roadmaps->present($user, $active) : null,
            'todo'         => $rows,
            'popular'      => $popular = $this->popular($chosen, $inList, $level),
            'more'         => $this->more($chosen, $inList->merge($popular->pluck('id'))),
            'otherHobbies' => Hobby::withCount('projects')->whereNotIn('id', $chosen)->orderBy('sort')->get(),
            'nextBadge'    => $this->nextBadge($rows),
            'tip'          => TipOfTheDay::for($hobbies),
            'goal'         => $this->dailyGoal($user, Str::before($user->name, ' ')),
        ]);
    }

    /** Populer: sebagian besar dari hobi yang dipilih, level pengguna lebih dulu, sisanya dari hobi lain. */
    private function popular(Collection $chosen, Collection $inList, string $level): Collection
    {
        $base = Project::with('hobby')->where('is_popular', true)->whereNotIn('id', $inList);

        $mine = (clone $base)
            ->whereIn('hobby_id', $chosen)
            ->orderByRaw('CASE WHEN level = ? THEN 0 ELSE 1 END', [$level])
            ->orderByDesc('learners_count')
            ->limit(6)
            ->get();

        if ($mine->count() >= 6) {
            return $mine;
        }

        $rest = (clone $base)
            ->whereNotIn('id', $mine->pluck('id'))
            ->where(fn ($q) => $q->whereNull('hobby_id')->orWhereNotIn('hobby_id', $chosen))
            ->orderByDesc('learners_count')
            ->limit(6 - $mine->count())
            ->get();

        return $mine->concat($rest)->values();
    }

    /** Konten tambahan di bagian bawah: tutorial dan proyek dari hobi lain atau umum. */
    private function more(Collection $chosen, Collection $exclude): Collection
    {
        return Project::with('hobby')
            ->whereNotIn('id', $exclude)
            ->where(fn ($q) => $q->whereNull('hobby_id')->orWhereNotIn('hobby_id', $chosen))
            ->orderByDesc('learners_count')
            ->limit(6)
            ->get();
    }

    private function nextBadge(Collection $rows): ?array
    {
        $tried = $rows
            ->filter(fn ($r) => $r->steps_done >= 1 && $r->project?->hobby_id)
            ->pluck('project.hobby_id')
            ->unique()
            ->count();

        $completed = $rows->filter(fn ($r) => $r->steps_done >= RoadmapService::TOTAL)->count();

        $badges = [
            ['name' => 'Penjelajah Penasaran', 'hint' => 'Coba 2 hobi', 'value' => $tried, 'target' => 2, 'unit' => 'hobi'],
            ['name' => 'Pembuat Pertama', 'hint' => 'Selesaikan 1 proyek', 'value' => $completed, 'target' => 1, 'unit' => 'proyek'],
        ];

        foreach ($badges as $badge) {
            if ($badge['value'] < $badge['target']) {
                return $badge;
            }
        }

        return null;
    }

    private function dailyGoal(User $user, string $firstName): array
    {
        $goal    = max(1, (int) ($user->daily_goal_minutes ?: 45));
        $minutes = (int) $user->dailyActivities()->where('date', today()->toDateString())->value('minutes');

        $dates = $user->dailyActivities()
            ->where('minutes', '>', 0)
            ->where('date', '>=', today()->subDays(60)->toDateString())
            ->pluck('date')
            ->map(fn ($d) => Carbon::parse($d)->toDateString())
            ->all();

        $streak = 0;
        $cursor = in_array(today()->toDateString(), $dates, true) ? today() : today()->subDay();

        while (in_array($cursor->toDateString(), $dates, true)) {
            $streak++;
            $cursor = $cursor->copy()->subDay();
        }

        $remaining = max(0, $goal - $minutes);

        $message = match (true) {
            $remaining === 0 => "Target hari ini tercapai, {$firstName}. Kamu sudah berkarya {$streak} hari berturut-turut.",
            $streak > 0      => "Terus melangkah, {$firstName}! Tinggal {$remaining} menit lagi untuk menjaga rangkaian {$streak} harimu.",
            default          => "Satu langkah kecil sudah cukup, {$firstName}. Coba berkarya {$remaining} menit hari ini.",
        };

        return [
            'goal'    => $goal,
            'minutes' => $minutes,
            'percent' => min(100, (int) round($minutes / $goal * 100)),
            'message' => $message,
        ];
    }
}
