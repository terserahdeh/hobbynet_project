<?php

namespace App\Http\Controllers;

use App\Models\StarterPack;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class BookmarkController extends Controller
{
    /** Halaman "Tersimpan": paket yang disimpan user, terbaru di atas. */
    public function index()
    {
        $packs = StarterPack::with(['project', 'items'])
            ->select('starter_packs.*')
            ->join('starter_pack_bookmarks as b', 'b.starter_pack_id', '=', 'starter_packs.id')
            ->where('b.user_id', Auth::id())
            ->orderByDesc('b.created_at')
            ->get()
            ->filter(fn (StarterPack $pack) => $pack->project)
            ->values();

        return view('bookmarks.index', [
            'packs'    => $packs,
            'savedIds' => $packs->pluck('id')->all(),
        ]);
    }

    /** Simpan / batal simpan. Dipakai tombol di kartu dan di halaman detail. */
    public function toggle(StarterPack $starterPack)
    {
        $row = DB::table('starter_pack_bookmarks')->where([
            'user_id'         => Auth::id(),
            'starter_pack_id' => $starterPack->id,
        ]);

        if ($row->exists()) {
            $row->delete();
            $saved = false;
        } else {
            DB::table('starter_pack_bookmarks')->insert([
                'user_id'         => Auth::id(),
                'starter_pack_id' => $starterPack->id,
                'created_at'      => now(),
                'updated_at'      => now(),
            ]);
            $saved = true;
        }

        $starterPack->loadMissing('project');

        return back()->with('bookmark', [
            'saved' => $saved,
            'title' => $starterPack->project->title ?? 'Paket',
        ]);
    }
}
