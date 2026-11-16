<?php

namespace App\Services;

use App\Models\Hobby;
use App\Models\Project;
use App\Models\RoadmapStep;
use App\Models\User;
use App\Support\PlusAccess;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

/**
 * Sumber konten "Proyek Populer" dan "Hobi Lainnya" di beranda.
 *
 * Isinya semua proyek yang punya langkah (roadmap hobi/level lain dan proyek berpemandu),
 * kecuali roadmap utama hobi yang sudah dipilih pengguna. Populer diambil lebih dulu
 * (paling banyak peserta); Hobi Lainnya memakai sisanya, jadi keduanya tidak tumpang tindih.
 */
class DiscoveryService
{
    private const LEVEL_ORDER = ['beginner' => 0, 'intermediate' => 1, 'advanced' => 2];

    /** @var array<int, Collection> */
    private array $cache = [];

    public function __construct(private RoadmapService $roadmaps)
    {
    }

    /** Proyek untuk "Proyek Populer" (koleksi Project, dengan atribut tambahan is_locked). */
    public function popular(User $user, int $limit = 6): Collection
    {
        return $this->pool($user)
            ->sortByDesc(fn ($item) => $item['project']->learners_count)
            ->take($limit)
            ->map(function ($item) {
                $item['project']->setAttribute('is_locked', $item['locked']);

                return $item['project'];
            })
            ->values();
    }

    /** Item untuk "Hobi Lainnya", tanpa proyek yang sudah tampil di Populer. */
    public function others(User $user, int $limit = 8, int $popularLimit = 6): Collection
    {
        $shown = $this->popular($user, $popularLimit)->pluck('id')->all();

        return $this->pool($user)
            ->reject(fn ($item) => in_array($item['project']->id, $shown, true))
            ->sort(fn ($a, $b) => $a['sort'] <=> $b['sort'])
            ->take($limit)
            ->values();
    }

    private function pool(User $user): Collection
    {
        if (isset($this->cache[$user->id])) {
            return $this->cache[$user->id];
        }

        $stats = RoadmapStep::query()
            ->selectRaw('project_id, COUNT(*) as steps, SUM(estimated_minutes) as minutes')
            ->groupBy('project_id')
            ->get()
            ->keyBy('project_id');

        if ($stats->isEmpty()) {
            return $this->cache[$user->id] = collect();
        }

        $level    = self::LEVEL_ORDER[$this->roadmaps->levelFor($user)] ?? 0;
        $chosen   = $user->hobbies()->pluck('hobbies.id')->all();
        $progress = DB::table('user_projects')->where('user_id', $user->id)->pluck('progress_percent', 'project_id');

        // Roadmap utama (satu per hobi) menurut level pengguna.
        $primary = Hobby::all()->mapWithKeys(fn (Hobby $h) => [$h->id => $this->roadmaps->findStarter($h, $user)?->id]);

        return $this->cache[$user->id] = Project::with('hobby')
            ->whereIn('id', $stats->keys())
            ->get()
            ->reject(fn (Project $p) => in_array($p->hobby_id, $chosen, true) && $primary->get($p->hobby_id) === $p->id)
            ->map(function (Project $p) use ($stats, $level, $chosen, $primary, $progress, $user) {
                $isPrimary = $p->hobby_id && $primary->get($p->hobby_id) === $p->id; // roadmap utama hobi yang belum dipilih
                $group     = $isPrimary ? 0 : (in_array($p->hobby_id, $chosen, true) ? 1 : 2);
                $distance  = abs((self::LEVEL_ORDER[$p->level] ?? 0) - $level);

                return [
                    'sort'     => [$group, $distance, -$p->learners_count],
                    'project'  => $p,
                    'href' => $isPrimary && $p->hobby
                        ? route('roadmap.show', [$p->hobby->slug, 'main' => 1])
                        : route('projects.show', $p->slug),
                    'kind'     => $p->is_starter ? 'Roadmap' : 'Proyek',
                    'hobby'    => $p->hobby?->short_name ?? $p->hobby?->name ?? $p->category,
                    'level'    => Project::levelLabel($p->level),
                    'steps'    => (int) $stats[$p->id]->steps,
                    'duration' => Project::humanDuration((int) $stats[$p->id]->minutes),
                    'percent'  => (int) ($progress[$p->id] ?? 0),
                    'locked'   => PlusAccess::locked($p, $user),
                    'premium'  => PlusAccess::isPremium($p),
                ];
            })
            ->values();
    }
}
