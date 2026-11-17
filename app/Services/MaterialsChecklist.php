<?php

namespace App\Services;

use App\Models\RoadmapStep;
use App\Models\StepMaterial;
use App\Models\User;
use Illuminate\Support\Facades\DB;

/**
 * "Meet Your Materials" state for one learner: which items they already own,
 * and the budget numbers shown in the Progres Persiapan card.
 */
class MaterialsChecklist
{
    /** @return int[] */
    public function ownedIds(User $user, RoadmapStep $step): array
    {
        return DB::table('step_material_user')
            ->join('step_materials', 'step_materials.id', '=', 'step_material_user.step_material_id')
            ->where('step_material_user.user_id', $user->id)
            ->where('step_materials.roadmap_step_id', $step->id)
            ->pluck('step_material_user.step_material_id')
            ->map(fn ($id) => (int) $id)
            ->all();
    }

    /** Flip ownership. Returns true when the item is now owned. */
    public function toggle(User $user, StepMaterial $material): bool
    {
        $row = DB::table('step_material_user')
            ->where('user_id', $user->id)
            ->where('step_material_id', $material->id);

        if ($row->exists()) {
            $row->delete();

            return false;
        }

        DB::table('step_material_user')->insert([
            'user_id' => $user->id,
            'step_material_id' => $material->id,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return true;
    }

    /**
     * @return array{required: \Illuminate\Support\Collection, optional: \Illuminate\Support\Collection, owned: int[], stats: array}
     */
    public function forStep(RoadmapStep $step, User $user): array
    {
        $owned = $this->ownedIds($user, $step);
        $materials = $step->materials;

        $required = $materials->reject->is_optional->values();
        $optional = $materials->filter->is_optional->values();

        $requiredOwned = $required->filter(fn ($m) => in_array($m->id, $owned, true));
        $toBuy = $required->reject(fn ($m) => in_array($m->id, $owned, true));

        $stats = [
            'total' => $required->count(),
            'owned' => $requiredOwned->count(),
            'remaining' => $toBuy->count(),
            'percent' => $required->count() ? (int) round($requiredOwned->count() / $required->count() * 100) : 100,
            'budget' => (int) $required->sum('price'),
            'to_buy' => (int) $toBuy->sum('price'),
            'ready' => $toBuy->isEmpty(),
        ];

        return compact('required', 'optional', 'owned', 'stats');
    }

    public static function rupiah(int|float|null $amount): string
    {
        return 'Rp ' . number_format((float) $amount, 0, ',', '.');
    }
}
