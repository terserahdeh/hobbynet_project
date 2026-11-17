<?php

namespace Database\Seeders;

use App\Models\RoadmapStep;
use App\Models\StepMaterial;
use Illuminate\Database\Seeder;

/**
 * Fills step_materials: roadmap_step_id, position, kind, name, spec,
 * is_optional, item_url, description, price. (image is left empty: upload it in admin.)
 *
 *   php artisan db:seed --class=StepMaterialSeeder
 *
 * - Every "materials" step gets the list below (matched by step + name, so re-running is safe).
 * - Rows that already exist keep anything you typed: only blank columns are filled.
 * - Prices are rough estimates in Rupiah.
 */
class StepMaterialSeeder extends Seeder
{
    /** [kind, name, spec, is_optional, description, price, search keyword] */
    private const ITEMS = [
        ['tool', 'Crochet hook', '4 mm', false, 'Kait rajut ukuran sedang, nyaman dipegang untuk pemula.', 25000, 'kait rajut 4mm'],
        ['material', 'Yarn', 'Medium weight (4), cotton works well', false, 'Benang katun lembut, mudah dilihat jahitannya.', 45000, 'benang katun rajut'],
        ['tool', 'Scissors', null, false, 'Gunting kecil untuk memotong benang.', 15000, 'gunting kecil benang'],
        ['tool', 'Tapestry needle', 'For weaving in ends', true, 'Jarum tumpul untuk merapikan ujung benang.', 12000, 'jarum tapestry'],
        ['tool', 'Stitch markers', 'Pack of 10', true, 'Penanda jahitan supaya tidak salah hitung.', 12000, 'stitch marker rajut'],
        ['tool', 'Measuring tape', null, true, 'Meteran kain untuk mengecek ukuran hasil rajutan.', 10000, 'meteran kain'],
    ];

    public function run(): void
    {
        RoadmapStep::where('type', 'materials')->get()->each(function (RoadmapStep $step) {
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
                    'item_url' => $m->item_url ?: 'https://www.tokopedia.com/search?q=' . urlencode($m->name),
                    'description' => $m->description ?: ($m->spec ?: $m->name),
                ])->save();
            });
    }
}