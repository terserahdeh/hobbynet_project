<?php

namespace App\Http\Controllers;

use App\Models\Hobby;
use App\Models\StarterPack;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class StarterPackController extends Controller
{
    /**
     * Halaman "Jelajahi" (juga dipakai route "search" dari kolom cari di navbar).
     * Filter: ?hobby={slug hobi}  dan  ?q={kata kunci}
     */
    public function index(Request $request)
    {
        $hobbies = Hobby::orderBy('id')->get();

        $hobby = $request->string('hobby')->toString();
        $hobby = $hobbies->contains('slug', $hobby) ? $hobby : null;
        $q     = $request->string('q')->trim()->toString();

        $packs = StarterPack::with(['project', 'items'])
            ->orderBy('id')
            ->get()
            ->filter(function (StarterPack $pack) use ($hobby, $q) {
                $project = $pack->project;

                if (! $project) {
                    return false;
                }
                // Project.icon berisi slug hobinya (crochet, painting, photography, ...)
                if ($hobby && $project->icon !== $hobby) {
                    return false;
                }
                if ($q !== '') {
                    $haystack = Str::lower(implode(' ', [$project->title, $project->summary, $project->category]));
                    if (! Str::contains($haystack, Str::lower($q))) {
                        return false;
                    }
                }

                return true;
            })
            ->values();

        return view('packs.index', [
            'packs'    => $packs,
            'hobbies'  => $hobbies,
            'hobby'    => $hobby,
            'q'        => $q,
            'savedIds' => $this->savedIds(),
        ]);
    }

    /** Detail satu paket persiapan. */
    public function show(StarterPack $starterPack)
    {
        $starterPack->load(['project', 'items']);
        abort_if(! $starterPack->project, 404);

        return view('packs.show', [
            'pack'    => $starterPack,
            'project' => $starterPack->project,
            'isSaved' => in_array($starterPack->id, $this->savedIds()),
        ]);
    }

    private function savedIds(): array
    {
        return DB::table('starter_pack_bookmarks')
            ->where('user_id', Auth::id())
            ->pluck('starter_pack_id')
            ->all();
    }
}
