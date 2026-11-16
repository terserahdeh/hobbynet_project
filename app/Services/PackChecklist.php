<?php

namespace App\Services;

use App\Models\Project;
use App\Models\StarterPack;
use App\Models\StarterPackItem;
use App\Models\User;
use Illuminate\Support\Facades\DB;

/** Satu sumber untuk checklist alat & bahan: item starter pack + status "sudah punya". */
class PackChecklist
{
    public function ownedIds(User $user, StarterPack $pack): array
    {
        return DB::table('starter_pack_item_user')
            ->where('user_id', $user->id)
            ->whereIn('starter_pack_item_id', $pack->items->pluck('id'))
            ->pluck('starter_pack_item_id')
            ->all();
    }

    /** Null jika proyek belum punya starter pack. */
    public function forProject(Project $project, User $user): ?array
    {
        $pack = StarterPack::with('items')->where('project_id', $project->id)->first();

        if (! $pack || $pack->items->isEmpty()) {
            return null;
        }

        $owned = $this->ownedIds($user, $pack);
        $essentials = $pack->items->where('is_essential', true);

        return [
            'pack' => $pack,
            'items' => $pack->items,
            'owned' => $owned,
            'ready' => $essentials->whereIn('id', $owned)->count(),
            'total' => $essentials->count(),
            'remaining' => (int) $essentials->whereNotIn('id', $owned)->sum('price'),
        ];
    }

    /** Balik status "sudah punya". Mengembalikan status barunya. */
    public function toggle(User $user, StarterPackItem $item): bool
    {
        $key = ['user_id' => $user->id, 'starter_pack_item_id' => $item->id];

        if (DB::table('starter_pack_item_user')->where($key)->delete()) {
            return false;
        }

        DB::table('starter_pack_item_user')->insertOrIgnore($key + ['created_at' => now(), 'updated_at' => now()]);

        return true;
    }
}