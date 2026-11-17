<?php

namespace App\Http\Controllers;

use App\Models\Hobby;
use App\Models\StarterPack;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use App\Models\RoadmapStep;
use App\Models\StarterPackItem;
use App\Services\PackChecklist;

class StarterPackController extends Controller
{
    /**
     * Halaman Jelajahi.
     * Filter: ?hobby={slug hobi} dan ?q={kata kunci}
     */
    public function index(Request $request)
    {
        $hobbies = Hobby::orderBy('id')->get();

        $hobby = $request->string('hobby')->toString();
        $hobby = $hobbies->contains('slug', $hobby)
            ? $hobby
            : null;

        $q = $request->string('q')->trim()->toString();

        $packs = StarterPack::with(['project', 'items'])
            ->orderBy('id')
            ->get()
            ->filter(function (StarterPack $pack) use ($hobby, $q) {
                $project = $pack->project;

                if (! $project) {
                    return false;
                }

                // Filter berdasarkan kategori hobi.
                if ($hobby && $project->icon !== $hobby) {
                    return false;
                }

                // Filter berdasarkan kata kunci pencarian.
                if ($q !== '') {
                    $haystack = Str::lower(
                        implode(' ', [
                            $project->title,
                            $project->summary,
                            $project->category,
                        ])
                    );

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

    /**
     * Detail satu paket persiapan.
     *
     * Pengguna gratis tetap boleh melihat informasi
     * paket, termasuk paket premium.
     */
    public function show(StarterPack $starterPack)
    {
        $starterPack->load(['project', 'items']);

        abort_if(! $starterPack->project, 404);

        return view('packs.show', [
            'pack'     => $starterPack,
            'project'  => $starterPack->project,
            'isSaved'  => in_array(
                $starterPack->id,
                $this->savedIds()
            ),
            'ownedIds' => app(PackChecklist::class)
                ->ownedIds(Auth::user(), $starterPack),
        ]);
    }

    /**
     * Mulai proyek dari halaman Jelajahi.
     *
     * Proyek gratis:
     * - Bisa langsung dibuka.
     *
     * Proyek premium:
     * - Pengguna Plus bisa membuka proyek.
     * - Pengguna gratis diarahkan ke halaman upgrade.
     */
    public function start(StarterPack $starterPack)
    {
        $starterPack->load('project');

        $project = $starterPack->project;

        abort_if(! $project, 404);

        // PERLINDUNGAN AKSES HOBBYNET PLUS
        if ($starterPack->is_premium) {

            $user = Auth::user();

            // Jika belum login, arahkan ke login.
            if (! $user) {
                return redirect()->route('login')
                    ->with(
                        'status',
                        'Silakan masuk untuk melanjutkan proyek HobbyNet Plus.'
                    );
            }

            // Jika belum berlangganan, arahkan ke upgrade.
            if (! $user->isPlus()) {
                return redirect()->route('plus')
                    ->with(
                        'status',
                        'Proyek ini eksklusif untuk member HobbyNet Plus. Upgrade sekarang untuk membuka roadmap dan mulai berkarya!'
                    );
            }
        }

        // Jika proyek belum memiliki langkah roadmap,
        // arahkan ke sistem course lama.
        if (! RoadmapStep::where(
            'project_id',
            $project->id
        )->exists()) {
            return redirect()->route(
                'courses.legacy',
                $starterPack
            );
        }

        // Pengguna yang berhak mengakses proyek
        // diarahkan ke halaman guided project.
        return redirect()->route(
            'projects.show',
            $project->slug
        );
    }

    /**
     * Mengambil ID paket yang disimpan pengguna.
     */
    private function savedIds(): array
    {
        return DB::table('starter_pack_bookmarks')
            ->where('user_id', Auth::id())
            ->pluck('starter_pack_id')
            ->all();
    }

    /**
     * Centang / batalkan "aku sudah punya"
     * untuk satu item perlengkapan.
     */
    public function toggleOwned(
        Request $request,
        StarterPackItem $item,
        PackChecklist $checklist
    ) {
        $owned = $checklist->toggle(
            $request->user(),
            $item
        );

        if ($request->expectsJson()) {
            return response()->json([
                'owned' => $owned,
            ]);
        }

        return back();
    }
}
