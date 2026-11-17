<?php

namespace Database\Seeders;

use App\Models\RoadmapStep;
use App\Models\StepMaterial;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

/**
 * Fills step_materials: roadmap_step_id, position, kind, name, spec,
 * is_optional, item_url, description, price. (image is left empty: upload it in admin.)
 *
 *   php artisan db:seed --class=StepMaterialSeeder
 *
 * - Only the CROCHET hobby's "materials" step gets the list below (matched by step + name, so re-running is safe).
 * - Rows that already exist keep anything you typed: only blank columns are filled.
 * - Prices are rough estimates in Rupiah.
 * - Names must stay identical to the material names in RoadmapSeeder::crochetSteps().
 */
class StepMaterialSeeder extends Seeder
{
    /** [kind, name, spec, is_optional, description, price, search keyword] */
    private const ITEMS = [
        ['tool', 'Hook rajut', '4 mm', false, 'Kait rajut ukuran sedang, nyaman dipegang untuk pemula.', 25000, 'kait rajut 4mm'],
        ['material', 'Benang', 'Ketebalan sedang (4), benang katun cocok dipakai', false, 'Benang katun lembut, mudah dilihat jahitannya.', 45000, 'benang katun rajut'],
        ['tool', 'Gunting', null, false, 'Gunting kecil untuk memotong benang.', 15000, 'gunting kecil benang'],
        ['tool', 'Jarum tapestry', 'Untuk merapikan ujung benang', true, 'Jarum tumpul untuk merapikan ujung benang.', 12000, 'jarum tapestry'],
        ['tool', 'Penanda tusukan', 'Isi 10 buah', true, 'Penanda jahitan supaya tidak salah hitung.', 12000, 'stitch marker rajut'],
        ['tool', 'Meteran kain', null, true, 'Meteran kain untuk mengecek ukuran hasil rajutan.', 10000, 'meteran kain'],
    ];

    /** Old English names, kept only so the cleanup below still finds rows written by earlier versions. */
    private const LEGACY_NAMES = [
        'Crochet hook', 'Yarn', 'Scissors', 'Tapestry needle', 'Stitch markers', 'Measuring tape',
    ];

    public function run(): void
    {
        $crochetProjectIds = DB::table('projects')
            ->join('hobbies', 'hobbies.id', '=', 'projects.hobby_id')
            ->where('hobbies.slug', 'crochet')
            ->pluck('projects.id');

        // Undo an earlier version of this seeder that put the crochet list on every hobby.
        // Only rows still carrying the exact text this seeder wrote are removed.
        $seeded = collect(self::ITEMS);
        StepMaterial::query()
            ->whereIn('roadmap_step_id', RoadmapStep::where('type', 'materials')->whereNotIn('project_id', $crochetProjectIds)->pluck('id'))
            ->whereIn('name', array_merge($seeded->pluck(1)->all(), self::LEGACY_NAMES))
            ->whereIn('description', $seeded->pluck(4))
            ->delete();

        RoadmapStep::where('type', 'materials')->whereIn('project_id', $crochetProjectIds)->get()->each(function (RoadmapStep $step) {
            foreach (self::ITEMS as $i => [$kind, $name, $spec, $optional, $description, $price, $keyword]) {
                $material = StepMaterial::firstOrNew([
                    'roadmap_step_id' => $step->id,
                    'name' => $name,
                ]);

                $defaults = [
                    'position' => $i + 1,
                    'kind' => $kind,
                    'spec' => $spec,
                    'is_optional' => $optional,
                    'description' => $description,
                    'price' => $price,
                    'item_url' => 'https://shopee.co.id/search?keyword=' . urlencode($keyword),
                ];

                foreach ($defaults as $column => $value) {
                    // New row: take everything. Existing row: only fill what is blank.
                    if (! $material->exists || blank($material->{$column})) {
                        $material->{$column} = $value;
                    }
                }

                $material->save();
            }
        });

        // Any other material that still has blanks (added by hand in admin).
        StepMaterial::query()->where(fn ($q) => $q->whereNull('price')->orWhereNull('item_url')->orWhereNull('description'))
            ->get()->each(function (StepMaterial $m) {
                $m->forceFill([
                    'price' => $m->price ?? ($m->kind === 'tool' ? 20000 : 30000),
                    'item_url' => $m->item_url ?: 'https://shopee.co.id/search?keyword=' . urlencode($m->name),
                    'description' => $m->description ?: ($m->spec ?: $m->name),
                ])->save();
            });
    }
}