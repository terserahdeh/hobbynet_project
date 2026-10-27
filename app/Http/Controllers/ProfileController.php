<?php

namespace App\Http\Controllers;

use App\Models\Badge;
use App\Models\ProjectSubmission;
use App\Services\BadgeService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ProfileController extends Controller
{
    public function __construct(private BadgeService $badgeService)
    {
    }

    public function show()
    {
        $user = Auth::user();

        $hobbies = $user->hobbies;

        // Semua lencana dari database; yang sudah diraih ditandai dari tabel badge_user.
        $earned = $user->badges()->get()->keyBy('id');

        $badges = Badge::orderBy('id')->get()->map(fn (Badge $badge) => [
            'name'      => $badge->name,
            'desc'      => $badge->description,
            'icon'      => $badge->icon,
            'tone'      => match ($badge->color) {
                'blue' => 'blue',
                'pink' => 'pink',
                default => 'sage',
            },
            'earned'    => $earned->has($badge->id),
            'earned_at' => $earned->get($badge->id)?->pivot->earned_at,
        ])->sortByDesc('earned')->values();

        $completed = ProjectSubmission::with(['project', 'photos'])
            ->where('user_id', $user->id)
            ->where('status', ProjectSubmission::STATUS_SUBMITTED)
            ->orderByDesc('submitted_at')
            ->get()
            ->filter(fn ($s) => $s->project)
            ->values();

        return view('profile.show', [
            'user'       => $user,
            'initial'    => mb_strtoupper(mb_substr(trim((string) $user->name), 0, 1)) ?: '?',
            'hobbies'    => $hobbies,
            'since'      => $user->created_at ? $user->created_at->locale('id')->translatedFormat('F Y') : null,
            'savedCount' => DB::table('starter_pack_bookmarks')->where('user_id', $user->id)->count(),
            'badges'     => $badges,
            'earnedCount' => $earned->count(),
            'nextBadge'  => $this->badgeService->nextFor($user),
            'completed'  => $completed,
        ]);
    }
}
