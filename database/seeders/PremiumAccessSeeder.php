<?php

namespace Database\Seeders;

use App\Models\Project;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Menentukan proyek mana yang gratis dan mana yang HobbyNet Plus.
 *
 * - Semua proyek roadmap (is_starter = true) GRATIS, di level mana pun.
 * - Proyek lain PREMIUM, kecuali yang ada di daftar ALSO_FREE.
 * - Starter pack di Jelajahi mengikuti status proyeknya (is_premium disamakan).
 *
 * Jalankan SETELAH semua seeder proyek (ProjectSeeder, GuidedProjectSeeder, ExplorePackSeeder, dst):
 *   php artisan db:seed --class=PremiumAccessSeeder
 * Aman diulang. Jalankan lagi setiap kali ada proyek baru.
 */
class PremiumAccessSeeder extends Seeder
{
    /** Slug proyek non-roadmap yang tetap gratis. Tambahkan di sini kalau ingin membuka proyek tertentu. */
    private const ALSO_FREE = [
        // 'scrunchie-rajut-sederhana',
    ];

    public function run(): void
    {
        Project::where('is_starter', true)->update(['is_premium' => false]);
        Project::where('is_starter', false)->update(['is_premium' => true]);

        if (self::ALSO_FREE !== []) {
            Project::whereIn('slug', self::ALSO_FREE)->update(['is_premium' => false]);
        }

        if (Schema::hasTable('starter_packs') && Schema::hasColumn('starter_packs', 'is_premium')) {
            foreach (Project::pluck('is_premium', 'id') as $projectId => $premium) {
                DB::table('starter_packs')->where('project_id', $projectId)->update(['is_premium' => (bool) $premium]);
            }
        }

        $premium = Project::where('is_premium', true)->count();
        $free = Project::where('is_premium', false)->count();

        $this->command?->info("Proyek gratis: {$free} | proyek Plus: {$premium}");
    }
}
