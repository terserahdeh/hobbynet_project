<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\BadgeRequest;
use App\Models\Badge;
use App\Models\RoadmapStep;
use App\Support\UniqueSlug;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;

class BadgeController extends Controller
{
    /** Awarded by the app itself, so it can't be removed. */
    private const PROTECTED_SLUGS = ['course-completer'];

    public function index(): View
    {
        return view('admin.badges.index', [
            'badges' => Badge::orderBy('name')->get(),
            'earned' => DB::table('badge_user')->selectRaw('badge_id, count(*) as total')->groupBy('badge_id')->pluck('total', 'badge_id'),
            'usedBy' => RoadmapStep::whereNotNull('badge_id')->selectRaw('badge_id, count(*) as total')->groupBy('badge_id')->pluck('total', 'badge_id'),
            'protected' => self::PROTECTED_SLUGS,
        ]);
    }

    public function create(): View
    {
        return view('admin.badges.form', ['badge' => new Badge(['icon' => 'default', 'color' => 'green'])]);
    }

    public function store(BadgeRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $badge = Badge::create($data + ['slug' => UniqueSlug::make($data['name'], 'badges')]);

        return redirect()->route('admin.badges.index')
            ->with('status', "“{$badge->name}” was added. Attach it to a step to hand it out.");
    }

    public function edit(Badge $badge): View
    {
        return view('admin.badges.form', ['badge' => $badge]);
    }

    public function update(BadgeRequest $request, Badge $badge): RedirectResponse
    {
        $badge->update($request->validated());

        return redirect()->route('admin.badges.index')->with('status', "“{$badge->name}” was updated.");
    }

    public function destroy(Badge $badge): RedirectResponse
    {
        if (in_array($badge->slug, self::PROTECTED_SLUGS, true)) {
            return back()->with('warning', "“{$badge->name}” is handed out automatically when a roadmap is finished, so it can't be deleted.");
        }

        $name = $badge->name;
        $badge->delete(); // steps that used it simply stop awarding a badge

        return redirect()->route('admin.badges.index')->with('status', "“{$name}” was deleted.");
    }
}
