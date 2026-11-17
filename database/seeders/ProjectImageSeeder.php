<?php

namespace Database\Seeders;

use App\Models\Project;
use App\Models\RoadmapStep;
use App\Models\StepInstruction;
use Illuminate\Database\Seeder;

/**
 * Foto proyek dan foto panduan langkah dari Unsplash (lisensi Unsplash: gratis dipakai, tanpa wajib atribusi).
 * Hanya mengubah kolom gambar; progres, checklist, dan isi langkah tidak disentuh, jadi aman di database tim.
 *
 * Jalankan setelah seeder konten (ProjectSeeder, RoadmapSeeder, RoadmapLevelSeeder, ExplorePackSeeder, dst):
 *   php artisan db:seed --class=ProjectImageSeeder
 * Mengganti foto: ubah ID di PROJECTS / STEP_GUIDES (bagian "photo-..." dari alamat images.unsplash.com).
 */
class ProjectImageSeeder extends Seeder
{
    /** slug proyek => ID foto Unsplash (deskripsi foto di komentar) */
    public const PROJECTS = [
        // Merajut
        'alas-gelas-rajut-katun'      => 'photo-1648217736318-fbc4abc138ec', // doily rajut kuning muda di atas selimut
        'scrunchie-rajut-sederhana'   => 'photo-1700144525165-0c534034b5f1', // scrunchie kain pink dan tosca
        'lap-rajut-persegi'           => 'photo-1782576417426-4ae01da20440', // granny square rajut kuning-oranye-pink
        'gantungan-kunci-amigurumi'   => 'photo-1753370230699-8e21227afeb6', // kelinci rajut memegang wortel
        'tas-jaring-belanja'          => 'photo-1656578087784-e0429200ed18', // tas mesh kuning
        'topi-beanie-rajut'           => 'photo-1674433859621-979ae6e39c1a', // topi rajut dengan pom-pom
        'syal-pola-kabel'             => 'photo-1557303696-f0a415dc1b3e',    // tekstur rajutan cokelat
        'amigurumi-karakter-lengkap'  => 'photo-1686151573986-03b5a79f22a5', // dua boneka rajut di rak kayu
        'bunga-rajut-mini'            => 'photo-1753366556705-1f657d2fa4db', // bunga matahari rajut
        'headband-rajut'              => 'photo-1773747203069-c4ed5b1200ac', // headband rajut hijau

        // Melukis
        'kartu-pos-cat-air'           => 'photo-1605013547670-e0d98161754d', // kartu ucapan bermotif bunga
        'gradasi-langit-senja'        => 'photo-1629654857513-1136aef1b10f', // sapuan cat air pink-oranye
        'bunga-kecil-dua-warna'       => 'photo-1748724366770-46500ded69d2', // lukisan cat air semak mawar pink
        'cat-air-botani-zen'          => 'photo-1777712917236-3217bdc973f6', // lukisan cat air bunga snowdrop
        'lanskap-gunung-berkabut'     => 'photo-1740508906488-a3631b429ca0', // lanskap biru berkabut dan bukit
        'stilleven-buah-cat-air'      => 'photo-1596548438137-d51ea5c83ca5', // lukisan buah hijau dan merah
        'potret-wajah-cat-air'        => 'photo-1635956563341-dd023a9c466f', // lukisan cat air mata perempuan
        'kota-hujan-basah'            => 'photo-1713727660607-4bff18618098', // ilustrasi orang berpayung di hujan
        'bookmark-cat-air'            => 'photo-1560719887-fe3105fa1e55',    // pembatas buku warna-warni
        'kaktus-cat-air'              => 'photo-1557379488-c611c67dab77',    // lukisan kaktus

        // Fotografi
        'cerita-foto-sehari-hari'     => 'photo-1523371683773-affcb4a2e39e', // memotret jalan dengan ponsel
        'potret-cahaya-pagi'          => 'photo-1631832293782-2eda7804489b', // cahaya jendela dan tanaman pot
        'jalan-sore-satu-warna'       => 'photo-1636861903715-f30a283fb068', // matahari sore di antara pohon
        'seri-foto-jalanan'           => 'photo-1484346791885-776ca38ab7fd', // fotografer dengan kamera di jalan
        'esai-foto-naratif'           => 'photo-1587022929815-c3fe3e95b22e', // fotografer hitam-putih dengan DSLR
        'foto-produk-sederhana'       => 'photo-1527011046414-4781f1f94f8c', // tanaman di samping softbox
        'potret-cahaya-buatan'        => 'photo-1767130298927-2df12c33e5d6', // studio foto dengan lampu
        'foto-makanan-sederhana'      => 'photo-1478145046317-39f10e56b5e9', // buah di piring dari atas
        'foto-langit-senja'           => 'photo-1500763702684-af70eba9a9f1', // memotret matahari terbenam dengan ponsel

        // Umum
        'roti-sourdough-pemula'       => 'photo-1767065888377-e5ffb4e39645', // roti sourdough di talenan
        'bordir-lingkaran-pemula'     => 'photo-1771479452306-6dad4a9fbec0', // bordir mawar kuning di hoop
        'pembatas-buku-bunga-kering'  => 'photo-1638294621924-be97f5f92413', // daun dan bunga kering
        'jurnal-kulit-klasik'         => 'photo-1762113246719-1a6adc837209', // jurnal bersampul kulit
        'cetak-linocut-sederhana'     => 'photo-1744096641619-646e1f6fbcd5', // hasil cetak linocut
        'lilin-aromaterapi-sederhana' => 'photo-1757688525739-8d1e13daf44f', // lilin putih dalam toples kaca
        'gantungan-kunci-makrame'     => 'photo-1715000103283-01ed4755483e', // simpul makrame dari tali
        'tas-tote-jahit-tangan'       => 'photo-1544816155-12df9643f363',    // tas kain krem
        'kokedama-tanaman-mini'       => 'photo-1760863264489-b051feacddaf', // bola lumut tanaman gantung
    ];

    /**
     * Foto panduan langkah yang file lokalnya belum ada.
     * slug proyek => [slug langkah => [ID foto, keterangan]]
     */
    public const STEP_GUIDES = [
        'kartu-pos-cat-air' => [
            'build-your-wash-skills' => ['photo-1629654857513-1136aef1b10f', 'Example: a soft watercolor wash'],
        ],
        'cerita-foto-sehari-hari' => [
            'build-your-composition-skills' => ['photo-1606512070436-b56e3105bbf7', 'Example: framing a scene with your phone'],
        ],
        'tas-jaring-belanja' => [
            'learn-the-mesh-stitch' => ['photo-1777537953560-10fda7d9965a', 'Example: crochet pieces with yarn and hook'],
        ],
        'syal-pola-kabel' => [
            'master-the-cable-cross' => ['photo-1773499129478-933a7a421f84', 'Example: chunky knit texture'],
        ],
        'lanskap-gunung-berkabut' => [
            'learn-layering-and-glazing' => ['photo-1713817130353-c79509fea26b', 'Example: layered hills and a lake'],
        ],
        'potret-wajah-cat-air' => [
            'mix-skin-tones-with-glazes' => ['photo-1644417089793-f2145c957736', 'Example: a painted portrait'],
        ],
        'seri-foto-jalanan' => [
            'learn-the-decisive-moment' => ['photo-1612442377806-9cb831587c6c', 'Example: a moment on the street'],
        ],
        'esai-foto-naratif' => [
            'master-consistent-light-and-style' => ['photo-1692759872978-268c37f65e4e', 'Example: one consistent black-and-white look'],
        ],
    ];

    public static function url(string $photoId, int $width = 1200): string
    {
        return "https://images.unsplash.com/{$photoId}?auto=format&fit=crop&w={$width}&q=80";
    }

    /** Foto proyek untuk slug ini, atau null jika belum ada di daftar. */
    public static function forProject(string $slug): ?string
    {
        return isset(self::PROJECTS[$slug]) ? self::url(self::PROJECTS[$slug]) : null;
    }

    public function run(): void
    {
        $done = 0;
        $missing = [];

        foreach (self::PROJECTS as $slug => $photoId) {
            $updated = Project::where('slug', $slug)->update(['image' => self::url($photoId)]);
            $updated ? $done++ : $missing[] = $slug;
        }

        $guides = 0;

        foreach (self::STEP_GUIDES as $projectSlug => $steps) {
            $projectId = Project::where('slug', $projectSlug)->value('id');

            if (! $projectId) {
                continue;
            }

            foreach ($steps as $stepSlug => [$photoId, $caption]) {
                $stepId = RoadmapStep::where('project_id', $projectId)->where('slug', $stepSlug)->value('id');

                if (! $stepId) {
                    continue;
                }

                // Gambar panduan dipasang di instruksi pertama, sama seperti seeder konten.
                $instructionId = StepInstruction::where('roadmap_step_id', $stepId)->orderBy('position')->value('id');

                if ($instructionId) {
                    $guides += StepInstruction::whereKey($instructionId)
                        ->update(['image' => self::url($photoId, 900), 'image_caption' => $caption]);
                }
            }
        }

        $this->command?->info("Foto proyek terpasang: {$done} | foto panduan langkah: {$guides}");

        if ($missing !== []) {
            $this->command?->warn('Proyek belum ada di database (jalankan seeder konten dulu): ' . implode(', ', $missing));
        }
    }
}
