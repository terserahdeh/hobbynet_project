<?php

namespace Database\Seeders;

use App\Models\Project;
use App\Models\RoadmapStep;
use App\Support\YouTube;
use Illuminate\Database\Seeder;

/**
 * Video YouTube HANYA di dalam langkah (halaman langkah), bukan di halaman roadmap atau detail proyek.
 *
 * Cara pakai: ganti 'https://www.youtube.com' (placeholder) dengan tautan video untuk langkah itu,
 * lalu jalankan:  php artisan db:seed --class=ProjectVideoSeeder
 *
 * - Placeholder dilewati diam-diam, tidak menampilkan apa pun dan tidak menimpa video yang sudah ada.
 * - Tautan boleh berbentuk watch?v=..., youtu.be/..., shorts/..., embed/..., atau langsung ID 11 karakter.
 * - Aman diulang. Seeder konten lain tidak menimpa kolom video.
 * - Langkah "Meet Your Materials" dan "Share" tidak dimasukkan. Kalau mau, tambahkan sendiri
 *   dengan slug 'meet-your-materials' (atau slug materials milik hobinya).
 * - Slug proyek: ProjectSeeder.php. Slug langkah: RoadmapSeeder.php, RoadmapLevelSeeder.php, GuidedProjectSeeder.php.
 */
class ProjectVideoSeeder extends Seeder
{
    private int $filled = 0;
    private int $placeholders = 0;
    private int $problems = 0;

    private function videos(): array
    {
        return [
            // Roadmap pemula: Merajut
            'alas-gelas-rajut-katun' => [
                'steps' => [
                    'learn-your-first-stitches' => 'https://www.youtube.com/watch?v=ovjqTYVFcEw',  // Learn Your First Stitches (lesson)
                    'build-your-basic-skills' => 'https://www.youtube.com/watch?v=fvUUMgAAjcg',  // Build Your Basic Skills (lesson)
                    'mini-practice-project' => 'https://www.youtube.com/watch?v=aAxGTnVNJiE&t=215s&pp=ygUjc2luZ2xlIGNyb2NoZXQgc3F1YXJlIGZvciBiZWdpbm5lcnM%3D',  // Follow a Mini Practice Project (practice)
                    'create-your-crochet-coaster' => 'https://youtu.be/9l55SV8zCFU?si=7tH-gXYBXRIxNwZf',  // Create Your Crochet Coaster (project)
                ],
            ],

            // Roadmap pemula: Melukis
            'kartu-pos-cat-air' => [
                'steps' => [
                    'build-your-wash-skills' => 'https://www.youtube.com/shorts/VmJ04fSKn9g',  // Build Your Basic Skills (lesson)
                    'mini-painting-practice' => 'https://www.youtube.com/shorts/aPwlCv03fL0',  // Follow a Mini Practice Project (practice)
                    'create-your-watercolor-postcard' => 'https://youtu.be/xUbvGYRsJUY?si=ur8D90epPli7pB6n',  // Create Your Watercolor Postcard (project)
                ],
            ],

            // Roadmap pemula: Fotografi
            'cerita-foto-sehari-hari' => [
                'steps' => [
                    'learn-to-see-light' => 'https://www.youtube.com',  // Learn to See the Light (lesson)
                    'build-your-composition-skills' => 'https://www.youtube.com',  // Build Your Basic Skills (lesson)
                    'mini-photo-practice' => 'https://www.youtube.com',  // Follow a Mini Practice Project (practice)
                    'create-your-photo-story' => 'https://www.youtube.com',  // Create Your Photo Story (project)
                ],
            ],

            // Roadmap menengah: Merajut
            'tas-jaring-belanja' => [
                'steps' => [
                    'refresh-your-foundations' => 'https://www.youtube.com/shorts/K8OA40XZRAI',  // Refresh Your Foundations (lesson)
                    'learn-the-mesh-stitch' => 'https://www.youtube.com/watch?v=u_8mGutOZ4g&pp=ygUTbWVzaCBzdGl0Y2ggY3JvY2hldA%3D%3D',  // Learn the Mesh Stitch (lesson)
                    'practice-a-mesh-circle' => 'https://www.youtube.com/shorts/P9jRS1HppiI',  // Practice a Mesh Circle (practice)
                    'crochet-your-market-bag' => 'https://www.youtube.com/watch?v=jF0ptr1FYYs&pp=ygUUc2hvcHBpbmcgYmFnIGNyb2NoZXQ%3D',  // Crochet Your Market Bag (project)
                ],
            ],

            // Roadmap mahir: Merajut
            'syal-pola-kabel' => [
                'steps' => [
                    'warm-up-with-post-stitches' => 'https://www.youtube.com/watch?v=_VD4vkQMMfo&pp=ygUoZG91YmxlIGNyb2NoZXQgZm91bmRhdGlvbiBjaGFpbiB0dXRvcmlhbA%3D%3D',  // Warm Up with Post Stitches (lesson)
                    'master-the-cable-cross' => 'https://www.youtube.com/watch?v=3V453Ad9o7E&pp=ygUdY2FibGUgY3JvY2hldCBzdGl0Y2ggdHV0b3JpYWw%3D',  // Master the Cable Cross (lesson)
                    'crochet-your-cable-scarf' => 'https://www.youtube.com/watch?v=iOe8yFh3xf0&pp=ygUUY2FibGUgc2NhcmYgY3JvY2hldCA%3D',  // Crochet Your Cable Scarf (project)
                ],
            ],

            // Roadmap menengah: Melukis
            'lanskap-gunung-berkabut' => [
                'steps' => [
                    'refresh-your-wash-skills' => 'https://www.youtube.com',  // Refresh Your Wash Skills (lesson)
                    'learn-layering-and-glazing' => 'https://www.youtube.com',  // Learn Layering and Glazing (lesson)
                    'practice-misty-edges' => 'https://www.youtube.com',  // Practice Misty Edges (practice)
                    'paint-your-misty-mountains' => 'https://www.youtube.com',  // Paint Your Misty Mountains (project)
                ],
            ],

            // Roadmap mahir: Melukis
            'potret-wajah-cat-air' => [
                'steps' => [
                    'study-proportions-and-values' => 'https://www.youtube.com/watch?v=1J7RQvKnWf4&pp=ygUjcHJvcG9ydGlvbnMgYW5kIHZhbHVlcyBpbiBwYWludGluZyA%3D',  // Study Proportions and Values (lesson)
                    'mix-skin-tones-with-glazes' => 'https://www.youtube.com/watch?v=5FBSWQIIj6w&pp=ygUdc2tpbiB0b25lIGdsYXplIGluIHdhdGVyY29sb3I%3D',  // Mix Skin Tones with Glazes (lesson)
                    'paint-a-single-eye-study' => 'https://www.youtube.com/watch?v=w4I1yFmRY2k&pp=ygUbcGFpbnRpbmcgZXllcyBpbiB3YXRlcmNvbG9y',  // Paint a Single Eye Study (practice)
                    'paint-your-portrait' => 'https://www.youtube.com/watch?v=fRCBlSKjZng&pp=ygUgcGFpbnRpbmcgcG9ydHJhaXRzIGluIHdhdGVyY29sb3I%3D',  // Paint Your Portrait (project)
                    ],
                ],

            // Roadmap menengah: Fotografi
            'seri-foto-jalanan' => [
                'steps' => [
                    'refresh-the-exposure-basics' => 'https://www.youtube.com',  // Refresh the Exposure Basics (lesson)
                    'learn-the-decisive-moment' => 'https://www.youtube.com',  // Learn the Decisive Moment (lesson)
                    'shoot-moving-subjects' => 'https://www.youtube.com',  // Shoot Moving Subjects (practice)
                    'shoot-your-street-series' => 'https://www.youtube.com',  // Shoot Your Street Series (project)
                ],
            ],

            // Roadmap mahir: Fotografi
            'esai-foto-naratif' => [
                'steps' => [
                    'define-your-story-angle' => 'https://www.youtube.com',  // Define Your Story Angle (lesson)
                    'master-consistent-light-and-style' => 'https://www.youtube.com',  // Master Consistent Light and Style (lesson)
                    'test-your-shot-list' => 'https://www.youtube.com',  // Test Your Shot List (practice)
                    'create-your-photo-essay' => 'https://www.youtube.com',  // Create Your Photo Essay (project)
                ],
            ],

            // Proyek berpemandu (pemula): Merajut
            'scrunchie-rajut-sederhana' => [
                'steps' => [
                    'chain-your-base' => 'https://www.youtube.com',  // Chain Your Base (lesson)
                    'practice-single-crochet-rounds' => 'https://www.youtube.com',  // Practice Single Crochet Rounds (practice)
                    'work-the-scrunchie-band' => 'https://www.youtube.com',  // Work the Scrunchie Band (project)
                    'finish-with-elastic' => 'https://www.youtube.com',  // Finish with Elastic (project)
                ],
            ],

            // Proyek berpemandu (menengah): Merajut
            'topi-beanie-rajut' => [
                'steps' => [
                    'crochet-the-crown' => 'https://www.youtube.com',  // Crochet the Crown (lesson)
                    'practice-working-even' => 'https://www.youtube.com',  // Practice Working Even (practice)
                    'build-the-beanie-body' => 'https://www.youtube.com',  // Build the Beanie Body (project)
                    'shape-the-brim-and-finish' => 'https://www.youtube.com',  // Shape the Brim and Finish (project)
                ],
            ],

            // Proyek berpemandu (mahir): Merajut
            'amigurumi-karakter-lengkap' => [
                'steps' => [
                    'shape-the-head-and-body' => 'https://www.youtube.com',  // Shape the Head and Body (lesson)
                    'practice-limbs-and-ears' => 'https://www.youtube.com',  // Practice Limbs and Ears (practice)
                    'assemble-your-character' => 'https://www.youtube.com',  // Assemble Your Character (project)
                    'add-details-and-finish' => 'https://www.youtube.com',  // Add Details and Finish (project)
                ],
            ],

            // Proyek berpemandu (pemula): Melukis
            'gradasi-langit-senja' => [
                'steps' => [
                    'wet-the-paper' => 'https://www.youtube.com',  // Wet the Paper (lesson)
                    'blend-two-colors' => 'https://www.youtube.com',  // Blend Two Colors (practice)
                    'add-your-silhouette' => 'https://www.youtube.com',  // Add Your Silhouette (project)
                    'dry-and-show-your-painting' => 'https://www.youtube.com',  // Dry and Show Your Painting (project)
                ],
            ],

            // Proyek berpemandu (menengah): Melukis
            'stilleven-buah-cat-air' => [
                'steps' => [
                    'sketch-simple-shapes' => 'https://www.youtube.com',  // Sketch Simple Shapes (lesson)
                    'build-light-and-shadow' => 'https://www.youtube.com',  // Build Light and Shadow (practice)
                    'layer-your-color' => 'https://www.youtube.com',  // Layer Your Color (project)
                    'add-details-and-cast-shadows' => 'https://www.youtube.com',  // Add Details and Cast Shadows (project)
                ],
            ],

            // Proyek berpemandu (mahir): Melukis
            'kota-hujan-basah' => [
                'steps' => [
                    'plan-composition-and-values' => 'https://www.youtube.com',  // Plan Composition and Values (lesson)
                    'paint-wet-reflections' => 'https://www.youtube.com',  // Paint Wet Reflections (practice)
                    'build-buildings-and-contrast' => 'https://www.youtube.com',  // Build Buildings and Contrast (project)
                    'add-highlights-and-finish' => 'https://www.youtube.com',  // Add Highlights and Finish (project)
                ],
            ],

            // Proyek berpemandu (pemula): Fotografi
            'potret-cahaya-pagi' => [
                'steps' => [
                    'find-your-window-light' => 'https://www.youtube.com',  // Find Your Window Light (lesson)
                    'try-three-angles' => 'https://www.youtube.com',  // Try Three Angles (practice)
                    'compose-your-best-shot' => 'https://www.youtube.com',  // Compose Your Best Shot (project)
                    'edit-and-pick-your-favorite' => 'https://www.youtube.com',  // Edit and Pick Your Favorite (project)
                ],
            ],

            // Proyek berpemandu (menengah): Fotografi
            'foto-produk-sederhana' => [
                'steps' => [
                    'build-a-simple-set' => 'https://www.youtube.com',  // Build a Simple Set (lesson)
                    'control-shadows' => 'https://www.youtube.com',  // Control Shadows (practice)
                    'shoot-multiple-angles' => 'https://www.youtube.com',  // Shoot Multiple Angles (project)
                    'edit-clean-and-consistent' => 'https://www.youtube.com',  // Edit Clean and Consistent (project)
                ],
            ],

            // Proyek berpemandu (mahir): Fotografi
            'potret-cahaya-buatan' => [
                'steps' => [
                    'plan-your-light-setup' => 'https://www.youtube.com',  // Plan Your Light Setup (lesson)
                    'shape-the-light' => 'https://www.youtube.com',  // Shape the Light (practice)
                    'shoot-your-portrait-series' => 'https://www.youtube.com',  // Shoot Your Portrait Series (project)
                    'retouch-and-deliver' => 'https://www.youtube.com',  // Retouch and Deliver (project)
                ],
            ],
        ];
    }

    public function run(): void
    {
        foreach ($this->videos() as $projectSlug => $def) {
            $project = Project::where('slug', $projectSlug)->first();

            if (! $project) {
                $this->command?->warn("Proyek \"{$projectSlug}\" tidak ditemukan.");
                $this->problems++;
                continue;
            }

            foreach ($def['steps'] ?? [] as $stepSlug => $url) {
                $this->save($project->id, $stepSlug, $url, "{$projectSlug} / {$stepSlug}");
            }
        }

        $this->command?->info("Video terpasang: {$this->filled} | masih placeholder: {$this->placeholders} | bermasalah: {$this->problems}");
    }

    private function save(int $projectId, string $stepSlug, string $url, string $label): void
    {
        if ($this->isPlaceholder($url)) {
            $this->placeholders++;
            return;
        }

        $id = YouTube::id($url);

        if (! $id) {
            $this->command?->warn("Tautan tidak valid untuk {$label}: {$url}");
            $this->problems++;
            return;
        }

        $query = RoadmapStep::where('project_id', $projectId)->where('slug', $stepSlug);

        if (! $query->exists()) {
            $this->command?->warn("Langkah tidak ditemukan: {$label} (cek slug-nya di seeder konten).");
            $this->problems++;
            return;
        }

        // Yang disimpan ID bersih, bukan teks mentah.
        $query->update(['video_url' => $id]);
        $this->filled++;
    }

    /** Beranda YouTube (tanpa path) dianggap placeholder. */
    private function isPlaceholder(string $url): bool
    {
        $parts = parse_url(trim($url));
        $host = preg_replace('/^(www|m)\./', '', strtolower($parts['host'] ?? ''));
        $path = $parts['path'] ?? '';

        return $host === 'youtube.com' && in_array($path, ['', '/'], true);
    }
}
