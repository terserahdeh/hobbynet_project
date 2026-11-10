<?php

namespace App\Http\Controllers;

use App\Models\Badge;
use App\Models\ProjectSubmission;
use App\Services\BadgeService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;

class ProfileController extends Controller
{
    public function __construct(private BadgeService $badgeService)
    {
    }

    public function edit()
    {
        return view('profile.edit', ['user' => Auth::user()]);
    }

    public function update(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'min:2', 'max:100'],
            'nickname' => ['nullable', 'string', 'min:2', 'max:40', 'regex:/^[\pL\pN_. -]+$/u'],
            'avatar' => ['nullable', 'image', 'mimes:jpeg,png,webp', 'max:2048'],
            'remove_avatar' => ['nullable', 'boolean'],
        ]);
        $user = $request->user();
        unset($data['avatar'], $data['remove_avatar']);
        if ($request->boolean('remove_avatar') && $user->avatar_path) {
            Storage::disk('public')->delete($user->avatar_path);
            $data['avatar_path'] = null;
        }
        if ($request->hasFile('avatar')) {
            $newPath = $request->file('avatar')->store('avatars', 'public');
            if ($user->avatar_path) Storage::disk('public')->delete($user->avatar_path);
            $data['avatar_path'] = $newPath;
        }
        $user->update($data);
        return redirect()->route('profile')->with('status', 'Profil berhasil diperbarui.');
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

        $submissions = ProjectSubmission::with(['project', 'photos'])
            ->where('user_id', $user->id)
            ->orderByDesc('updated_at')
            ->get()
            ->filter(fn ($s) => $s->project)
            ->values();

        $courseWorks = \App\Models\CourseSubmission::with('starterPack.project')
            ->where('user_id', $user->id)->where('status', 'submitted')->latest('submitted_at')->get();

        // Unified presentation collection: keep existing tables intact.
        $works = $submissions->where('status', ProjectSubmission::STATUS_SUBMITTED)
            ->map(function ($sub) {
                return [
                    'title' => $sub->title ?: $sub->project->title,
                    'course' => $sub->project->title,
                    'description' => $sub->reflection,
                    'cover' => $sub->photos->first()?->url,
                    'photo_count' => $sub->photos->count(),
                    'date' => $sub->submitted_at,
                    'url' => route('roadmap.work.show', $sub),
                ];
            })
            ->concat($courseWorks->map(function ($work) {
                $photos = $work->photos ?? [];
                return [
                    'title' => $work->title,
                    'course' => $work->starterPack?->project?->title ?? 'Course Jelajah',
                    'description' => $work->reflection,
                    'cover' => count($photos)? Storage::url($photos[0]): null,
                    'photo_count' => count($photos),
                    'date' => $work->submitted_at,
                    'url' => route('courses.work.show', $work),
                ];
            }))
            ->sortByDesc(fn ($work) => $work['date']?->timestamp ?? 0)
            ->values();

        return view('profile.show', [
            'works' => $works,
            'courseWorks' => $courseWorks,
            'user'       => $user,
            'initial'    => mb_strtoupper(mb_substr(trim((string) $user->name), 0, 1)) ?: '?',
            'hobbies'    => $hobbies,
            'since'      => $user->created_at ? $user->created_at->locale('id')->translatedFormat('F Y') : null,
            'savedCount' => DB::table('starter_pack_bookmarks')->where('user_id', $user->id)->count(),
            'badges'     => $badges,
            'earnedCount' => $earned->count(),
            'nextBadge'  => $this->badgeService->nextFor($user),
            'completed'  => $submissions->where('status', ProjectSubmission::STATUS_SUBMITTED)->values(),
            'drafts'     => $submissions->where('status', ProjectSubmission::STATUS_DRAFT)->values(),
        ]);
    }
}
