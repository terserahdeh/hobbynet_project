<?php

namespace Database\Seeders;

use App\Models\Badge;
use App\Models\Hobby;
use App\Models\Project;
use App\Models\RoadmapStep;
use Illuminate\Database\Seeder;

/**
 * Seeds badges and the beginner roadmap of every hobby (Crochet, Painting, Photography).
 * Safe to run again: steps are matched by (project, slug) and updated in place.
 * Run: php artisan db:seed --class=RoadmapSeeder
 */
class RoadmapSeeder extends Seeder
{
    public function run(): void
    {
        $badges = $this->seedBadges();

        // hobi => [method langkah, proyek cadangan jika belum ada starter pemula]
        $roadmaps = [
            'crochet' => ['crochetSteps', [
                'slug' => 'beginner-crochet-coaster', 'title' => 'Tatakan Gelas Rajut untuk Pemula', 'category' => 'Crochet',
                'summary' => 'Buat tatakan gelas yang lembut dan sederhana dengan satu teknik dasar.', 'minutes' => 150,
            ]],
            'painting' => ['paintingSteps', [
                'slug' => 'beginner-watercolor-postcard', 'title' => 'Kartu Pos Cat Air untuk Pemula', 'category' => 'Painting',
                'summary' => 'Lukis kartu pos kecil dengan sapuan warna yang lembut.', 'minutes' => 120,
            ]],
            'photography' => ['photographySteps', [
                'slug' => 'beginner-photo-story', 'title' => 'Cerita Foto Kehidupan Sehari-hari', 'category' => 'Photography',
                'summary' => 'Ceritakan harimu lewat lima foto dari ponsel.', 'minutes' => 120,
            ]],
        ];

        foreach ($roadmaps as $hobbySlug => [$method, $fallback]) {
            $hobby = Hobby::where('slug', $hobbySlug)->first();

            if (! $hobby) {
                $this->command?->warn("No \"{$hobbySlug}\" hobby found. Seed hobbies first.");
                continue;
            }

            // Starter PEMULA milik hobi ini (bukan starter menengah/mahir).
            $project = Project::where('hobby_id', $hobby->id)->where('is_starter', true)->where('level', 'beginner')->orderBy('id')->first()
                ?? Project::create([
                    'hobby_id' => $hobby->id,
                    'slug' => $fallback['slug'],
                    'title' => $fallback['title'],
                    'category' => $fallback['category'],
                    'summary' => $fallback['summary'],
                    'level' => 'beginner',
                    'duration_minutes' => $fallback['minutes'],
                    'color' => $hobby->color,
                    'is_starter' => true,
                ]);

            $this->seedSteps($project, $this->{$method}($badges), $badges);

            // Learners only see published roadmaps (admins can hide it again in /admin).
            $project->forceFill(['roadmap_published' => true])->save();

            $this->command?->info("Roadmap {$hobbySlug}: {$project->slug}");
        
        // Once a roadmap has steps it is managed in /admin. Re-seeding would overwrite
        // those edits and reset learners' ticked checklist items, so leave it alone.
        // if (RoadmapStep::where('project_id', $project->id)->exists()) {
        //     $this->command?->info('The crochet roadmap already has steps, so it was left untouched. Edit it in /admin.');
        //     return;
        // }
        }
        $this->call(\Database\Seeders\StepMaterialSeeder::class);
    }

    private function seedSteps(Project $project, array $steps, array $badges): void
    {
        foreach ($steps as $position => $data) {
            $step = RoadmapStep::updateOrCreate(
                ['project_id' => $project->id, 'slug' => $data['slug']],
                [
                    'position' => $position + 1,
                    'title' => $data['title'],
                    'summary' => $data['summary'],
                    'objective' => $data['objective'] ?? null,
                    'type' => $data['type'],
                    'estimated_minutes' => $data['minutes'],
                    'encouragement' => $data['encouragement'] ?? null,
                    'icon' => $data['icon'] ?? 'default',
                    'badge_id' => $data['badge'] ? $badges[$data['badge']]->id : null,
                ],
            );

            $step->materials()->delete();
            $step->instructions()->delete();
            $step->checklistItems()->delete();

            foreach ($data['materials'] ?? [] as $i => [$kind, $name, $spec, $optional]) {
                $step->materials()->create([
                    'position' => $i + 1, 'kind' => $kind, 'name' => $name,
                    'spec' => $spec, 'is_optional' => $optional,
                ]);
            }
            // 'image' => [public path, caption] is attached to the first instruction
            foreach ($data['instructions'] ?? [] as $i => $body) {
                $step->instructions()->create([
                    'position' => $i + 1,
                    'body' => $body,
                    'image' => $i === 0 ? ($data['image'][0] ?? null) : null,
                    'image_caption' => $i === 0 ? ($data['image'][1] ?? null) : null,
                ]);
            }
            foreach ($data['checklist'] ?? [] as $i => $label) {
                $step->checklistItems()->create(['position' => $i + 1, 'label' => $label]);
            }
        }
    }

    /** @return array<string, Badge> */
    private function seedBadges(): array
    {
        $rows = [
            'curious-explorer' => ['Penjelajah Penasaran', 'Karena sudah menyiapkan perlengkapan dan mengambil langkah pertama.', 'compass', 'green'],
            'stitch-master' => ['Master Tusukan', 'Karena sudah mempelajari tusukan single crochet.', 'stitch', 'blue'],
            'wash-master' => ['Master Wash', 'Karena sudah belajar melukis wash cat air yang halus dan merata.', 'brush', 'blue'],
            'light-spotter' => ['Pemburu Cahaya', 'Karena sudah belajar melihat dan memanfaatkan cahaya di sekitarmu.', 'camera', 'blue'],
            'first-creation' => ['Karya Pertama', 'Karena sudah menyelesaikan dan membagikan proyek pertamamu.', 'sparkle', 'pink'],
            'course-completer' => ['Penuntas Kursus', 'Karena sudah menyelesaikan semua checkpoint dalam sebuah roadmap.', 'check', 'green'],
            'plus-member' => ['Plus Member', 'Mendukung HobbyNet dengan paket Plus.', 'star', 'pink'],
        ];

        $badges = [];
        foreach ($rows as $slug => [$name, $description, $icon, $color]) {
            $badges[$slug] = Badge::updateOrCreate(
                ['slug' => $slug],
                compact('name', 'description', 'icon', 'color'),
            );
        }

        return $badges;
    }

    private function crochetSteps(array $badges): array
    {
        return [
            [
                'slug' => 'meet-your-materials',
                'title' => 'Kenali Bahan-Bahanmu',
                'summary' => 'Siapkan hook dan benang rajutmu.',
                'objective' => 'Tahu apa yang dibutuhkan dan menaruhnya dalam jangkauan',
                'type' => 'materials',
                'minutes' => 10,
                'icon' => 'yarn',
                'badge' => 'curious-explorer',
                'encouragement' => 'Awal yang bagus! Menaruh semuanya di satu tempat membuat langkah selanjutnya terasa mudah.',
                'materials' => [
                    ['tool', 'Hook rajut', '4 mm', false],
                    ['material', 'Benang', 'Ketebalan sedang (4), benang milk-cotton atau akrilik', false],
                    ['tool', 'Gunting', null, false],
                    ['tool', 'Jarum tapestry', 'Untuk merapikan ujung benang', true],
                    ['tool', 'Penanda tusukan', 'Untuk menandai tusukanmu', true],
                ],
                'instructions' => [
                    'Letakkan hook, benang, dan gunting di atas meja dengan cahaya yang cukup.',
                    'Periksa label benang. Di label seharusnya tertulis ketebalan sedang (medium) atau angka 4.',
                    'Pegang hook dengan cara yang nyaman buatmu, seperti memegang pensil. Tidak ada cara yang salah.',
                ],
                'checklist' => [
                    'Kumpulkan hook dan benangmu',
                    'Pastikan label benang bertuliskan ketebalan sedang (4)',
                ],
            ],
            [
                'slug' => 'learn-your-first-stitches',
                'title' => 'Pelajari Tusukan Pertamamu',
                'summary' => 'Buat simpul hidup dan rantai.',
                'objective' => 'Membuat simpul hidup dan rantai 12',
                'type' => 'lesson',
                'minutes' => 20,
                'icon' => 'knot',
                'badge' => null,
                'encouragement' => 'Setiap perajut pernah mulai dengan rantai yang bergelombang. Rantaimu boleh begitu juga.',
                'materials' => [
                    ['tool', 'Hook rajut', '4 mm', false],
                    ['material', 'Benang', 'Ketebalan sedang', false],
                ],
                'instructions' => [
                    'Buat lingkaran dari benang, tarik lingkaran kedua melaluinya, lalu masukkan ke hook. Itulah simpul hidupmu.',
                    'Lilitkan benang pada hook dan tarik melalui lingkaran di hook. Itu menghasilkan satu tusukan rantai.',
                    'Ulangi sampai kamu punya 12 rantai. Jaga agar tetap longgar sehingga hook mudah bergeser.',
                ],
                'checklist' => [
                    'Buat simpul hidup',
                    'Buat rantai 12 tusukan',
                    'Hitung rantaimu sekali lagi untuk memastikan',
                ],
            ],
            [
                'slug' => 'build-your-basic-skills',
                'title' => 'Bangun Keterampilan Dasarmu',
                'summary' => 'Pelajari tusukan single crochet.',
                'objective' => 'Mempelajari single crochet',
                'type' => 'lesson',
                'minutes' => 30,
                'icon' => 'hook',
                'badge' => 'stitch-master',
                'encouragement' => 'Hampir setengah jalan! Tusukan single crochet adalah inti dari kebanyakan proyek.',
                'image' => ['images/roadmap/single-crochet.jpg', 'Panduan Visual: Tusukan Single Crochet'],
                'materials' => [
                    ['tool', 'Hook rajut', '4 mm', false],
                    ['material', 'Benang', 'Ketebalan sedang', false],
                ],
                'instructions' => [
                    'Masukkan hook ke tusukan berikutnya, lilitkan benang, lalu tarik satu lingkaran. Sekarang ada dua lingkaran di hookmu.',
                    'Lilitkan benang lagi dan tarik melalui kedua lingkaran di hook. Itu melengkapi single crochet pertamamu.',
                    'Ulangi sepanjang baris. Jaga tegangan benang tetap rata: tidak terlalu kencang, tidak terlalu longgar.',
                ],
                'checklist' => [
                    'Selesaikan 10 baris single crochet',
                    'Jaga tegangan benang tetap konsisten',
                    'Hitung tusukanmu di setiap baris (jaga tetap 12)',
                ],
            ],
            [
                'slug' => 'mini-practice-project',
                'title' => 'Ikuti Proyek Latihan Mini',
                'summary' => 'Coba tusukan barumu pada kotak latihan kecil.',
                'objective' => 'Menyelesaikan kotak latihan kecil',
                'type' => 'practice',
                'minutes' => 30,
                'icon' => 'square',
                'badge' => null,
                'encouragement' => 'Kotak latihan itu untuk belajar, bukan untuk sempurna. Miring-miring pun tidak apa-apa.',
                'instructions' => [
                    'Buat rantai 12, lalu single crochet kembali sepanjang baris.',
                    'Balik rajutanmu, buat rantai 1, lalu single crochet sepanjang baris lagi.',
                    'Teruskan sampai rajutanmu berbentuk kira-kira persegi, lalu potong benang dan tarik ujungnya melalui lingkaran terakhir.',
                ],
                'checklist' => [
                    'Buat kotak latihan selebar sekitar 8 cm',
                    'Kunci benangmu (fasten off)',
                ],
            ],
            [
                'slug' => 'create-your-crochet-coaster',
                'title' => 'Buat Tatakan Gelas Rajutmu',
                'summary' => 'Rajut tatakan gelasmu, putaran demi putaran.',
                'objective' => 'Menyelesaikan satu tatakan gelas',
                'type' => 'project',
                'minutes' => 40,
                'icon' => 'coaster',
                'badge' => null,
                'encouragement' => 'Ini dia! Istirahatlah kapan pun kamu mau.',
                'materials' => [
                    ['tool', 'Hook rajut', '4 mm', false],
                    ['material', 'Benang katun', 'Ketebalan sedang', false],
                    ['tool', 'Jarum tapestry', 'Untuk merapikan ujung benang', false],
                ],
                'instructions' => [
                    'Buat magic ring, atau buat rantai 4 lalu sambung dengan slip stitch hingga membentuk lingkaran kecil.',
                    'Rajut tusukan single crochet ke dalam lingkaran, tambahkan tusukan secara merata di tiap putaran agar tatakan tetap datar.',
                    'Saat diameternya sekitar 10 cm, kunci benang dan rapikan ujungnya dengan jarum tapestry.',
                ],
                'checklist' => [
                    'Kerjakan putaran penambahan tusukan',
                    'Kunci benang dan rapikan ujungnya',
                    'Periksa apakah tatakanmu terletak datar',
                ],
            ],
            [
                'slug' => 'share-your-first-creation',
                'title' => 'Bagikan Karya Pertamamu',
                'summary' => 'Tambahkan foto dan beberapa kata tentang apa yang kamu buat.',
                'objective' => 'Merayakan proyek pertamamu yang selesai',
                'type' => RoadmapStep::TYPE_SHARE,
                'minutes' => 10,
                'icon' => 'sparkle',
                'badge' => 'first-creation',
                'encouragement' => 'Setiap master pernah menjadi pemula. Ini adalah mahakarya pertamamu!',
            ],
        ];
    }

    private function paintingSteps(array $badges): array
    {
        return [
            [
                'slug' => 'meet-your-painting-materials',
                'title' => 'Kenali Bahan-Bahanmu',
                'summary' => 'Siapkan cat, kuas, dan kertasmu.',
                'objective' => 'Tahu apa yang dibutuhkan dan menaruhnya dalam jangkauan',
                'type' => 'materials',
                'minutes' => 10,
                'icon' => 'brush',
                'badge' => 'curious-explorer',
                'encouragement' => 'Awal yang bagus! Meja yang rapi membuat melukis terasa tenang dan mudah.',
                'materials' => [
                    ['tool', 'Kuas bulat (round brush)', 'Ukuran 6', false],
                    ['material', 'Cat air', 'Satu set pan kecil sudah cukup', false],
                    ['material', 'Kertas cat air', '300 gsm, ukuran kartu pos atau A6', false],
                    ['tool', 'Gelas air dan tisu dapur', null, false],
                    ['tool', 'Masking tape', 'Untuk menahan kertas agar tetap rata', true],
                ],
                'instructions' => [
                    'Bersihkan meja di dekat jendela, lalu tata cat, kuas, kertas, dan gelas berisi air bersih.',
                    'Taruh tisu di sampingmu. Kamu akan memakainya untuk menyerap kelebihan air dari kuas.',
                    'Rekatkan keempat sudut kertas ke meja agar tetap rata saat basah.',
                ],
                'checklist' => [
                    'Kumpulkan kuas, cat, dan kertasmu',
                    'Isi gelas dengan air bersih',
                    'Rekatkan kertasmu ke meja',
                ],
            ],
            [
                'slug' => 'wet-your-brush-and-mix',
                'title' => 'Belajar Membasahi dan Mencampur',
                'summary' => 'Kenali bagaimana air dan warna bekerja.',
                'objective' => 'Mencampur satu warna dan membuat tiga sapuan',
                'type' => 'lesson',
                'minutes' => 20,
                'icon' => 'drop',
                'badge' => null,
                'encouragement' => 'Cat air suka memberi kejutan. Kejutan adalah bagian dari serunya.',
                'materials' => [
                    ['tool', 'Kuas bulat (round brush)', 'Ukuran 6', false],
                    ['material', 'Cat air', null, false],
                ],
                'instructions' => [
                    'Celupkan kuas ke air, sentuhkan ke pan cat, lalu putar di sudut kertas cadangan untuk membuat genangan warna.',
                    'Tambahkan sedikit air agar warna lebih muda, atau sedikit cat agar warna lebih pekat.',
                    'Buat tiga sapuan pendek: satu pucat, satu sedang, satu gelap. Perhatikan bagaimana warnanya berubah.',
                ],
                'checklist' => [
                    'Campur satu warna yang kamu suka',
                    'Lukis sapuan pucat, sedang, dan gelap',
                    'Bilas kuasmu setiap kali berganti warna',
                ],
            ],
            [
                'slug' => 'build-your-wash-skills',
                'title' => 'Bangun Keterampilan Dasarmu',
                'summary' => 'Belajar melukis wash yang halus dan merata.',
                'objective' => 'Melukis satu flat wash dan satu gradasi lembut',
                'type' => 'lesson',
                'minutes' => 30,
                'icon' => 'wave',
                'badge' => 'wash-master',
                'encouragement' => 'Setengah jalan! Wash yang baik adalah dasar hampir semua lukisan cat air.',
                'image' => ['images/roadmap/watercolor-wash.jpg', 'Panduan Visual: Flat Wash dan Gradasi'],
                'materials' => [
                    ['tool', 'Kuas bulat (round brush)', 'Ukuran 6', false],
                    ['material', 'Cat air', null, false],
                    ['material', 'Kertas latihan', 'Selembar kertas cadangan atau bagian belakang kertasmu', false],
                ],
                'instructions' => [
                    'Miringkan papanmu sedikit. Ambil banyak warna dengan kuas, lalu lukis satu garis lebar di bagian atas persegi panjang kecil.',
                    'Selagi garis itu masih basah, lukis garis berikutnya tepat di bawahnya agar kedua tepinya menyatu. Teruskan sampai ke bawah.',
                    'Untuk gradasi, tambahkan sedikit air pada kuasmu di setiap garis sehingga warna memudar ke arah bawah.',
                ],
                'checklist' => [
                    'Lukis satu flat wash di persegi panjang kecil',
                    'Lukis satu gradasi dari gelap ke terang',
                    'Biarkan keduanya kering sepenuhnya sebelum disentuh',
                ],
            ],
            [
                'slug' => 'mini-painting-practice',
                'title' => 'Ikuti Proyek Latihan Mini',
                'summary' => 'Lukis matahari terbenam mungil untuk mencoba keterampilan barumu.',
                'objective' => 'Menyelesaikan satu lukisan latihan kecil',
                'type' => 'practice',
                'minutes' => 30,
                'icon' => 'sunset',
                'badge' => null,
                'encouragement' => 'Lukisan latihan itu untuk belajar, bukan untuk sempurna. Miring-miring pun tidak apa-apa.',
                'instructions' => [
                    'Basahi seluruh kertas latihan dengan air bersih secara tipis.',
                    'Teteskan warna hangat (kuning atau oranye) di dekat bagian bawah dan warna dingin (biru atau ungu) di dekat bagian atas. Biarkan keduanya bertemu dan menyatu.',
                    'Biarkan kering, lalu tambahkan bentuk gelap kecil, seperti bukit atau pohon, di sepanjang bagian bawah dengan warna yang lebih pekat.',
                ],
                'checklist' => [
                    'Padukan dua warna menjadi langit',
                    'Tambahkan satu bentuk gelap kecil',
                    'Biarkan lukisan kering dalam posisi datar',
                ],
            ],
            [
                'slug' => 'create-your-watercolor-postcard',
                'title' => 'Buat Kartu Pos Cat Airmu',
                'summary' => 'Lukis kartu posmu, lapisan demi lapisan.',
                'objective' => 'Menyelesaikan satu lukisan kartu pos',
                'type' => 'project',
                'minutes' => 40,
                'icon' => 'postcard',
                'badge' => null,
                'encouragement' => 'Ini dia! Istirahatlah dan biarkan setiap lapisan mengering.',
                'materials' => [
                    ['tool', 'Kuas bulat (round brush)', 'Ukuran 6', false],
                    ['material', 'Cat air', null, false],
                    ['material', 'Kertas cat air', 'Ukuran kartu pos', false],
                    ['tool', 'Pulpen ujung halus', 'Untuk menambahkan detail kecil', true],
                ],
                'instructions' => [
                    'Pilih objek sederhana: langit, bunga, atau beberapa helai daun. Buat sketsa tipis dengan pensil jika kamu mau.',
                    'Lukis area yang paling besar dan paling terang lebih dulu dengan wash. Biarkan kering.',
                    'Tambahkan warna yang lebih gelap dan detail kecil paling akhir. Lepaskan lakban perlahan setelah kertas benar-benar kering.',
                ],
                'checklist' => [
                    'Lukis wash terangmu lebih dulu',
                    'Tambahkan bentuk gelap dan detail',
                    'Lepaskan lakban saat sudah benar-benar kering',
                ],
            ],
            [
                'slug' => 'share-your-first-painting',
                'title' => 'Bagikan Karya Pertamamu',
                'summary' => 'Tambahkan foto dan beberapa kata tentang apa yang kamu lukis.',
                'objective' => 'Merayakan proyek pertamamu yang selesai',
                'type' => RoadmapStep::TYPE_SHARE,
                'minutes' => 10,
                'icon' => 'sparkle',
                'badge' => 'first-creation',
                'encouragement' => 'Setiap master pernah menjadi pemula. Ini adalah mahakarya pertamamu!',
            ],
        ];
    }

    private function photographySteps(array $badges): array
    {
        return [
            [
                'slug' => 'meet-your-camera',
                'title' => 'Kenali Kameramu',
                'summary' => 'Siapkan ponsel atau kameramu dan bersihkan lensanya.',
                'objective' => 'Mengenal kameramu dan menaruhnya dalam jangkauan',
                'type' => 'materials',
                'minutes' => 10,
                'icon' => 'camera',
                'badge' => 'curious-explorer',
                'encouragement' => 'Awal yang bagus! Kamera terbaik adalah yang sudah kamu bawa.',
                'materials' => [
                    ['tool', 'Ponsel atau kamera', 'Apa pun yang kamu punya boleh', false],
                    ['tool', 'Kain lembut', 'Untuk membersihkan lensa', false],
                    ['material', 'Buku catatan kecil', 'Untuk mencatat ide', true],
                ],
                'instructions' => [
                    'Bersihkan lensa kameramu dengan lembut memakai kain lembut. Lensa yang bersih membuat setiap foto lebih tajam.',
                    'Buka aplikasi kamera dan cari pengaturan grid. Aktifkan. Grid akan membantumu menempatkan objek di dalam bingkai.',
                    'Ambil satu foto percobaan dari apa saja di dekatmu, sekadar memastikan semuanya berfungsi.',
                ],
                'checklist' => [
                    'Bersihkan lensa kameramu',
                    'Aktifkan grid di aplikasi kameramu',
                    'Ambil satu foto percobaan',
                ],
            ],
            [
                'slug' => 'learn-to-see-light',
                'title' => 'Belajar Melihat Cahaya',
                'summary' => 'Perhatikan dari mana cahaya datang dan bagaimana jatuhnya.',
                'objective' => 'Mengambil tiga foto dengan tiga jenis cahaya',
                'type' => 'lesson',
                'minutes' => 20,
                'icon' => 'sun',
                'badge' => null,
                'encouragement' => 'Fotografi dimulai dari memperhatikan. Kamu sudah melakukannya.',
                'materials' => [
                    ['tool', 'Ponsel atau kamera', null, false],
                ],
                'instructions' => [
                    'Berdirilah di dekat jendela dan perhatikan sisi mana dari sebuah benda yang terang dan sisi mana yang berada dalam bayangan.',
                    'Foto benda yang sama tiga kali: dengan cahaya dari samping, dari depan, dan dari belakangnya.',
                    'Lihat ketiganya. Mana yang terasa paling menarik bagimu? Tidak ada jawaban yang salah.',
                ],
                'checklist' => [
                    'Ambil satu foto dengan cahaya samping',
                    'Ambil satu foto dengan cahaya depan',
                    'Ambil satu foto dengan cahaya dari belakang',
                ],
            ],
            [
                'slug' => 'build-your-composition-skills',
                'title' => 'Bangun Keterampilan Dasarmu',
                'summary' => 'Pelajari aturan sepertiga dan pembingkaian sederhana.',
                'objective' => 'Mengambil foto dengan aturan sepertiga',
                'type' => 'lesson',
                'minutes' => 30,
                'icon' => 'grid',
                'badge' => 'light-spotter',
                'encouragement' => 'Setengah jalan! Pembingkaian yang mengubah sekadar jepretan menjadi sebuah foto.',
                'image' => ['images/roadmap/rule-of-thirds.jpg', 'Panduan Visual: Aturan Sepertiga'],
                'materials' => [
                    ['tool', 'Ponsel atau kamera', 'Grid sudah diaktifkan', false],
                ],
                'instructions' => [
                    'Lihat grid di kameramu. Grid membagi gambar menjadi sembilan kotak yang sama besar. Empat titik pertemuan garisnya adalah posisi yang paling kuat.',
                    'Tempatkan objekmu di salah satu titik itu, bukan tepat di tengah, lalu ambil fotonya.',
                    'Ambil foto kedua dari objek yang sama setelah mendekat, lalu foto ketiga setelah berpindah ke sudut yang berbeda.',
                ],
                'checklist' => [
                    'Ambil 5 foto dengan objek di titik grid',
                    'Coba satu foto dari jarak dekat',
                    'Coba satu foto dari sudut rendah atau tinggi',
                ],
            ],
            [
                'slug' => 'mini-photo-practice',
                'title' => 'Ikuti Proyek Latihan Mini',
                'summary' => 'Berjalan singkat dan foto satu warna.',
                'objective' => 'Menyelesaikan satu set kecil foto latihan',
                'type' => 'practice',
                'minutes' => 30,
                'icon' => 'walk',
                'badge' => null,
                'encouragement' => 'Foto latihan itu untuk belajar, bukan untuk sempurna. Jaga matamu tetap penasaran.',
                'instructions' => [
                    'Pilih satu warna, misalnya hijau atau kuning, lalu berjalan singkat di dalam atau di luar ruangan.',
                    'Foto semua hal menarik yang kamu temukan dengan warna itu. Coba manfaatkan cahaya dan grid yang sudah kamu latih.',
                    'Pilih tiga favoritmu. Perhatikan apa yang membuatmu tertarik.',
                ],
                'checklist' => [
                    'Ambil sedikitnya 10 foto dengan warna pilihanmu',
                    'Pilih 3 foto favoritmu',
                ],
            ],
            [
                'slug' => 'create-your-photo-story',
                'title' => 'Buat Cerita Fotomu',
                'summary' => 'Ceritakan harimu dalam lima foto.',
                'objective' => 'Menyelesaikan cerita lima foto',
                'type' => 'project',
                'minutes' => 40,
                'icon' => 'story',
                'badge' => null,
                'encouragement' => 'Ini dia! Momen kecil sehari-hari adalah cerita terbaik.',
                'materials' => [
                    ['tool', 'Ponsel atau kamera', null, false],
                    ['material', 'Buku catatan kecil', 'Untuk merencanakan lima jepretanmu', true],
                ],
                'instructions' => [
                    'Pilih cerita sederhana: pagimu, sebuah jalan-jalan, atau sebuah makanan. Tulis lima momen yang ingin kamu abadikan.',
                    'Ambil dua atau tiga foto untuk setiap momen, dengan memakai cahaya dan pembingkaian yang sudah kamu latih.',
                    'Pilih foto terbaik untuk setiap momen dan susun berurutan, dari awal sampai akhir.',
                ],
                'checklist' => [
                    'Rencanakan lima momen untuk ceritamu',
                    'Pilih foto terbaik untuk setiap momen',
                    'Susun kelima foto secara berurutan',
                ],
            ],
            [
                'slug' => 'share-your-first-photo-story',
                'title' => 'Bagikan Karya Pertamamu',
                'summary' => 'Tambahkan fotomu dan beberapa kata tentang ceritamu.',
                'objective' => 'Merayakan proyek pertamamu yang selesai',
                'type' => RoadmapStep::TYPE_SHARE,
                'minutes' => 10,
                'icon' => 'sparkle',
                'badge' => 'first-creation',
                'encouragement' => 'Setiap master pernah menjadi pemula. Ini adalah cerita fotomu yang pertama!',
            ],
        ];
    }
}
