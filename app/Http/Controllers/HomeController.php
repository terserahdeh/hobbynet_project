<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\User;
use App\Services\BadgeService;
use App\Support\TipOfTheDay;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Illuminate\View\View;

class HomeController extends Controller
{
    public function __construct(private BadgeService $badges)
    {
    }

    public function __invoke(Request $request): View|RedirectResponse
    {
        $user = $request->user();

        // Onboarding belum selesai: arahkan ke langkah yang tertinggal.
        if ($user->onboarding_step < 4) {
            return redirect()->route($user->nextOnboardingRoute());
        }

        $hobbies = $user->hobbies()
            ->with(['projects' => fn ($q) => $q->where('is_starter', true)->orderBy('id')])
            ->orderBy('sort')
            ->get();

        $progress = $user->projectProgress()->with('project')->get()->keyBy('project_id');

        return view('home', [
            'firstName'  => Str::before($user->name, ' '),
            'hobbies'    => $hobbies,
            'current'    => $this->currentProject($hobbies, $progress),
            'hobbyCards' => $this->hobbyCards($hobbies, $progress),
            'popular'    => $this->popularProjects($hobbies),
            'nextBadge'  => $this->nextBadge($user),
            'tip'        => TipOfTheDay::for($hobbies),
            'goal'       => $this->dailyGoal($user),
        ]);
    }

    /** Proyek yang paling pantas dilanjutkan, atau proyek pertama dari hobi terpilih. */
    private function currentProject(Collection $hobbies, Collection $progress): ?array
    {
        $row = $progress
            ->filter(fn ($p) => $p->progress_percent >= 1 && $p->progress_percent < 100)
            ->sortByDesc(fn ($p) => optional($p->last_worked_at)->timestamp ?? 0)
            ->first()
            ?? $progress->first(fn ($p) => $p->progress_percent === 0);

        if ($row) {
            return $this->present($row->project, $row->progress_percent);
        }

        $project = $hobbies->first()?->projects->first();

        return $project ? $this->present($project, 0) : null;
    }

    private function present(Project $project, int $percent): array
    {
        $left = (int) max(5, round($project->duration_minutes * (100 - $percent) / 100 / 5) * 5);

        return [
            'project'      => $project,
            'percent'      => $percent,
            'time_left'    => Project::humanDuration($left),
        ];
    }

    /** Satu kartu per hobi terpilih: proyek pertama + statusnya. */
    private function hobbyCards(Collection $hobbies, Collection $progress): Collection
    {
        return $hobbies
            ->map(function ($hobby) use ($progress) {
                $project = $hobby->projects->first();

                if (! $project) {
                    return null;
                }

                $row = $progress->get($project->id);

                [$status, $label] = match (true) {
                    $row === null                   => ['none', 'Belum dimulai'],
                    $row->progress_percent >= 100   => ['done', 'Selesai'],
                    $row->progress_percent >= 30    => ['doing', 'Sedang dikerjakan'],
                    default                         => ['new', 'Baru dimulai'],
                };

                return compact('hobby', 'project', 'status', 'label');
            })
            ->filter()
            ->values();
    }

    /** Proyek populer dari hobi lain, terpisah jelas dari rekomendasi personal. */
    private function popularProjects(Collection $hobbies): Collection
    {
        $ids = $hobbies->pluck('id');

        return Project::query()
            ->where('is_popular', true)
            ->where(fn ($q) => $q->whereNull('hobby_id')->orWhereNotIn('hobby_id', $ids))
            ->orderByDesc('learners_count')
            ->limit(6)
            ->get();
    }

    /** Lencana berikutnya dari roadmap (data nyata dari tabel badges / badge_user). */
    private function nextBadge(User $user): ?array
    {
        $next = $this->badges->nextFor($user);

        return $next ? [
            'name'   => $next['badge']->name,
            'hint'   => 'Selesaikan "' . $next['title'] . '"',
            'value'  => $next['value'],
            'target' => $next['target'],
        ] : null;
    }

    private function dailyGoal(User $user): array
    {
        $goal    = max(1, (int) ($user->daily_goal_minutes ?: 45));
        $minutes = (int) $user->dailyActivities()->whereDate('date', today())->value('minutes');

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
            $remaining === 0 => "Target hari ini tercapai. Kamu sudah berkarya {$streak} hari berturut-turut.",
            $streak > 0      => "Tinggal {$remaining} menit lagi untuk menjaga rangkaian {$streak} harimu.",
            default          => "Satu langkah kecil sudah cukup. Coba berkarya {$remaining} menit hari ini.",
        };

        return [
            'goal'    => $goal,
            'minutes' => $minutes,
            'percent' => min(100, (int) round($minutes / $goal * 100)),
            'message' => $message,
        ];
    }
}
