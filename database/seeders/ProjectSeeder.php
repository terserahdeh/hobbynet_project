<?php

namespace Database\Seeders;

use App\Models\Hobby;
use App\Models\Project;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;

/**
 * Katalog proyek lengkap: 3 hobi x 3 level (pemula, menengah, mahir) + 5 proyek umum.
 * Proyek starter (is_starter) menjadi roadmap; sisanya proyek berpemandu / Jelajahi.
 * Aman dijalankan ulang (dicocokkan lewat slug).
 *
 * Urutan seeder yang disarankan:
 *   ProjectSeeder -> RoadmapSeeder -> RoadmapLevelSeeder -> GuidedProjectSeeder
 *   -> StarterPackSeeder -> ExplorePackSeeder -> PremiumAccessSeeder -> ProjectVideoSeeder
 */
class ProjectSeeder extends Seeder
{
    public function run(): void
    {
        if (! Hobby::exists()) {
            $this->call(HobbySeeder::class);
        }

        $hobbyIds = Hobby::pluck('id', 'slug');

        // Kolom "skill" hanya diisi kalau ada di tabel projects.
        $hasSkill = Schema::hasColumn('projects', 'skill');

        // Ilustrasi yang sudah tersedia di public/images (dari ProjectSeeder lama).
        // Proyek lain memakai images/projects/{slug}.jpg; kalau file belum ada, kartu memakai gradasi.
        $images = [
            'alas-gelas-rajut-katun'     => 'images/landing/hero-3.png',
            'kartu-pos-cat-air'          => 'images/landing/hero-1.png',
            'cerita-foto-sehari-hari'    => 'images/landing/hero-2.png',
            'cat-air-botani-zen'         => 'images/landing/path-2.png',
            'roti-sourdough-pemula'      => 'images/landing/how-2.png',
            'jurnal-kulit-klasik'        => 'images/landing/how-1.png',
            'bordir-lingkaran-pemula'    => 'images/landing/path-1.png',
            'pembatas-buku-bunga-kering' => 'images/landing/how-3.png',
            'cetak-linocut-sederhana'    => 'images/landing/win-4.png',
        ];

        // Deskripsi khusus dari ProjectSeeder lama (dipertahankan).
        $descriptions = [
            'alas-gelas-rajut-katun'  => 'Fokus pada pola melingkar hari ini. Kamu makin lancar dengan tusuk tunggal ganda.',
            'kartu-pos-cat-air'       => 'Mulai dari dua warna dan satu kuas. Biarkan air yang bekerja.',
            'cerita-foto-sehari-hari' => 'Susun lima foto dari ponselmu menjadi satu cerita pendek tentang harimu.',
        ];

        $save = function (string $slug, array $data) use ($hasSkill, $images, $descriptions) {
            if (! $hasSkill) {
                unset($data['skill']);
            }

            $data['image'] = $images[$slug] ?? "images/projects/{$slug}.jpg";
            $data['description'] = $descriptions[$slug] ?? $data['description'];

            Project::updateOrCreate(['slug' => $slug], $data);
        };

        // [level, slug, judul, ringkasan, teknik utama, durasi (menit), peserta, proyek pertama untuk level itu?]
        $catalog = [
            'crochet' => ['Merajut', 'pink', [
                ['beginner', 'alas-gelas-rajut-katun', 'Alas Gelas Rajut Katun', 'Menguasai rantai dasar dan putaran pertama.', 'tusuk tunggal dan lingkaran ajaib', 50, 31000, true],
                ['beginner', 'scrunchie-rajut-sederhana', 'Scrunchie Rajut Sederhana', 'Satu lingkaran rajut yang bisa langsung kamu pakai.', 'tusuk tunggal berulang', 40, 12000, false],
                ['beginner', 'lap-rajut-persegi', 'Lap Rajut Persegi', 'Latihan tusuk dasar sambil membuat sesuatu yang berguna.', 'tusuk tunggal baris lurus', 60, 9800, false],
                ['beginner', 'gantungan-kunci-amigurumi', 'Gantungan Kunci Amigurumi Mini', 'Bola kecil yang berubah menjadi tokoh lucu.', 'rajut melingkar spiral', 90, 9000, false],
                ['intermediate', 'tas-jaring-belanja', 'Tas Jaring Belanja', 'Tas lentur dari pola jaring yang berulang.', 'tusuk jaring dan pola berulang', 150, 7400, true],
                ['intermediate', 'topi-beanie-rajut', 'Topi Beanie Rajut', 'Topi hangat dengan bentuk melingkar yang rapi.', 'rajut melingkar dengan pengurangan tusuk', 180, 6800, false],
                ['advanced', 'syal-pola-kabel', 'Syal Pola Kabel', 'Syal bertekstur dengan pola kabel yang menawan.', 'tusuk timbul dan pola kabel', 300, 3900, true],
                ['advanced', 'amigurumi-karakter-lengkap', 'Amigurumi Karakter Lengkap', 'Karakter tiga dimensi dengan bagian yang dijahit.', 'pembentukan 3D dan penyambungan bagian', 360, 4500, false],
            ]],
            'painting' => ['Melukis', 'sage', [
                ['beginner', 'kartu-pos-cat-air', 'Kartu Pos Cat Air', 'Belajar menyapu warna basah dalam satu lembar kecil.', 'sapuan basah di atas basah', 45, 18000, true],
                ['beginner', 'gradasi-langit-senja', 'Gradasi Langit Senja', 'Belajar memadukan dua warna menjadi langit sore.', 'gradasi dua warna', 30, 11000, false],
                ['beginner', 'bunga-kecil-dua-warna', 'Bunga Kecil dengan Dua Warna', 'Bentuk sederhana, warna lembut, tanpa tekanan.', 'sapuan kelopak sederhana', 35, 8700, false],
                ['beginner', 'cat-air-botani-zen', 'Cat Air Botani yang Menenangkan', 'Daun dan ranting dengan sapuan lembut.', 'sapuan daun yang lembut', 20, 12000, false],
                ['intermediate', 'lanskap-gunung-berkabut', 'Lanskap Gunung Berkabut', 'Lapisan transparan yang membentuk suasana berkabut.', 'lapisan transparan (glazing)', 90, 9200, true],
                ['intermediate', 'stilleven-buah-cat-air', 'Stilleven Buah dengan Cat Air', 'Buah sederhana dengan bayangan dan volume.', 'bayangan dan volume', 120, 7100, false],
                ['advanced', 'potret-wajah-cat-air', 'Potret Wajah dengan Cat Air', 'Wajah dengan warna kulit dan lapisan yang halus.', 'warna kulit dan lapisan halus', 240, 3600, true],
                ['advanced', 'kota-hujan-basah', 'Pemandangan Kota Saat Hujan', 'Refleksi, kontras tinggi, dan suasana basah.', 'refleksi dan kontras tinggi', 210, 3100, false],
            ]],
            'photography' => ['Fotografi', 'peri', [
                ['beginner', 'cerita-foto-sehari-hari', 'Cerita Foto Sehari-hari', 'Menangkap cahaya dan bayangan di sekitarmu.', 'komposisi dasar dan aturan sepertiga', 120, 14000, true],
                ['beginner', 'potret-cahaya-pagi', 'Potret Cahaya Pagi di Rumah', 'Cahaya jendela membuat benda biasa tampak istimewa.', 'membaca arah cahaya', 45, 7600, false],
                ['beginner', 'jalan-sore-satu-warna', 'Jalan Sore Berburu Satu Warna', 'Berjalan santai sambil mencari satu warna yang sama.', 'mata untuk detail dan warna', 60, 6900, false],
                ['intermediate', 'seri-foto-jalanan', 'Seri Foto Jalanan', 'Menangkap momen kecil di jalan dengan komposisi dinamis.', 'momen dan komposisi dinamis', 180, 8100, true],
                ['intermediate', 'foto-produk-sederhana', 'Foto Produk Sederhana', 'Latar bersih dan cahaya lembut untuk benda kecil.', 'pencahayaan lembut dan latar bersih', 150, 6400, false],
                ['advanced', 'esai-foto-naratif', 'Esai Foto Naratif', 'Rangkaian foto yang bercerita dengan gayamu sendiri.', 'narasi visual dan konsistensi gaya', 360, 3300, true],
                ['advanced', 'potret-cahaya-buatan', 'Potret dengan Cahaya Buatan', 'Mengatur cahaya dan bayangan untuk potret yang dramatis.', 'kontrol cahaya dan bayangan', 240, 2900, false],
            ]],
        ];

        foreach ($catalog as $hobbySlug => [$category, $color, $items]) {
            foreach ($items as [$level, $slug, $title, $summary, $skill, $minutes, $learners, $starter]) {
                $save($slug, [
                    'hobby_id'         => $hobbyIds[$hobbySlug] ?? null,
                    'title'            => $title,
                    'category'         => $category,
                    'summary'          => $summary,
                    'skill'            => $skill,
                    'description'      => "{$summary} Kamu akan berlatih {$skill}.",
                    'level'            => $level,
                    'duration_minutes' => $minutes,
                    'learners_count'   => $learners,
                    'icon'             => $hobbySlug,
                    'color'            => $color,
                    'is_starter'       => $starter,
                    'is_popular'       => true,
                ]);
            }
        }

        // Konten umum di luar tiga hobi awal (tanpa hobi)
        // [kategori, warna, ikon, level, slug, judul, ringkasan, teknik, durasi, peserta]
        $general = [
            ['Memanggang', 'pink', 'baking', 'beginner', 'roti-sourdough-pemula', 'Roti Sourdough untuk Pemula', 'Dari starter sederhana sampai roti pertamamu.', 'merawat starter dan melipat adonan', 4320, 45000],
            ['Kriya', 'sage', 'crafting', 'beginner', 'bordir-lingkaran-pemula', 'Bordir Lingkaran untuk Pemula', 'Tiga tusuk dasar di atas kain dalam bingkai.', 'tusuk dasar bordir', 75, 21000],
            ['Kriya', 'pink', 'crafting', 'beginner', 'pembatas-buku-bunga-kering', 'Pembatas Buku Bunga Kering', 'Mengawetkan bunga kecil menjadi pembatas buku.', 'mengeringkan dan menata bunga', 40, 9500],
            ['Kriya', 'peri', 'crafting', 'intermediate', 'jurnal-kulit-klasik', 'Jurnal Kulit Klasik', 'Jilid sampul kulit untuk jurnal harianmu.', 'menjahit jilid dan memotong kulit', 120, 8000],
            ['Seni Cetak', 'peri', 'crafting', 'intermediate', 'cetak-linocut-sederhana', 'Cetak Linocut Sederhana', 'Ukir satu motif kecil dan cetak berkali-kali.', 'mengukir dan mencetak', 90, 6200],
        ];

        foreach ($general as [$category, $color, $icon, $level, $slug, $title, $summary, $skill, $minutes, $learners]) {
            $save($slug, [
                'hobby_id'         => null,
                'title'            => $title,
                'category'         => $category,
                'summary'          => $summary,
                'skill'            => $skill,
                'description'      => "{$summary} Kamu akan berlatih {$skill}.",
                'level'            => $level,
                'duration_minutes' => $minutes,
                'learners_count'   => $learners,
                'icon'             => $icon,
                'color'            => $color,
                'is_starter'       => false,
                'is_popular'       => true,
            ]);
        }
    }
}