<?php

namespace App\Support;

use App\Models\Project;
use App\Models\User;

/**
 * Isi roadmap (6 langkah) dan daftar bahan, disesuaikan dengan:
 *  - level proyek (pemula / menengah / mahir)
 *  - alat yang sudah dimiliki pengguna
 *  - anggaran pilihan pengguna
 */
class StepBlueprints
{
    public const ORDER = ['intro', 'materials', 'basics', 'project', 'polish', 'mastery'];

    public const LABELS = [
        'intro' => 'Pengantar', 'materials' => 'Bahan', 'basics' => 'Dasar',
        'project' => 'Proyek', 'polish' => 'Poles', 'mastery' => 'Mahir',
    ];

    /** Porsi waktu tiap langkah dari total durasi proyek. */
    private const WEIGHTS = [
        'intro' => 0.05, 'materials' => 0.08, 'basics' => 0.25,
        'project' => 0.40, 'polish' => 0.12, 'mastery' => 0.10,
    ];

    private const HOBBIES = [
        'crochet' => [
            'noun' => 'merajut', 'practice' => 'satu baris latihan',
            'finish' => 'rapikan ujung benang dan ratakan hasil rajutanmu', 'show' => 'foto hasil rajutanmu',
        ],
        'painting' => [
            'noun' => 'melukis', 'practice' => 'satu lembar latihan sapuan',
            'finish' => 'tambahkan detail kecil lalu biarkan lukisanmu mengering sempurna', 'show' => 'foto lukisanmu',
        ],
        'photography' => [
            'noun' => 'memotret', 'practice' => 'sepuluh foto latihan',
            'finish' => 'pilih foto terbaik dan sunting seperlunya', 'show' => 'rangkaian fotomu',
        ],
        'generic' => [
            'noun' => 'berkarya', 'practice' => 'satu percobaan kecil',
            'finish' => 'rapikan hasil karyamu', 'show' => 'hasil karyamu',
        ],
    ];

    /** [judul, ringkasan, poin-poin] per langkah dan level. */
    private const TEXT = [
        'intro' => [
            'beginner' => ['Kenali dulu', 'Lihat hasil akhir {project} dan alur langkahnya. Tidak perlu menghafal apa pun, cukup tahu arahnya.',
                ['Lihat contoh hasil akhir {project}', 'Baca ringkasan keenam langkahnya', 'Ingat: mulai kecil sudah cukup']],
            'intermediate' => ['Segarkan ingatan', 'Ulas singkat dasar {noun} yang sudah kamu kuasai, lalu lihat apa yang baru di {project}.',
                ['Ulas dasar yang sudah kamu tahu', 'Tandai bagian yang terasa baru', 'Tentukan hasil yang ingin kamu capai']],
            'advanced' => ['Peta tantangan', 'Cari bagian {project} yang paling menantang, lalu tentukan targetmu sendiri sebelum mulai.',
                ['Temukan bagian tersulit {project}', 'Tulis satu target pribadi', 'Pikirkan sentuhan gayamu sendiri']],
        ],
        'basics' => [
            'beginner' => ['Latih dasar', 'Tujuan langkah ini: kenali {skill} dan selesaikan {practice}. Dasar ini penting untuk langkah-langkah berikutnya.',
                ['Pelajari {skill} pelan-pelan', 'Selesaikan {practice}', 'Ulangi bagian yang masih terasa canggung']],
            'intermediate' => ['Kuasai teknik baru', 'Tujuan langkah ini: pahami {skill} dan selesaikan {practice} dengan hasil yang lebih rapi dari biasanya.',
                ['Pelajari {skill} dan bedanya dengan teknik yang sudah kamu kuasai', 'Selesaikan {practice}', 'Periksa kerapian dan konsistensinya']],
            'advanced' => ['Asah teknik lanjutan', 'Tujuan langkah ini: asah {skill} sampai konsisten, lalu jadikan {practice} sebagai ujiannya.',
                ['Latih {skill} dengan teliti dan stabil', 'Selesaikan {practice} tanpa mengulang', 'Catat variasi yang ingin kamu coba']],
        ],
        'project' => [
            'beginner' => ['Buat {project}', 'Sekarang gabungkan semuanya untuk membuat {project}. Ambil jeda kapan pun kamu perlu.',
                ['Kerjakan {project} bagian demi bagian', 'Istirahat sejenak bila lelah', 'Lanjutkan sampai bentuknya utuh']],
            'intermediate' => ['Kerjakan {project}', 'Terapkan {skill} dalam {project}. Kamu bebas menyesuaikan ukuran dan warna.',
                ['Kerjakan {project} dengan {skill}', 'Cek hasilnya di setiap tahap', 'Sesuaikan ukuran atau warna sesukamu']],
            'advanced' => ['Wujudkan {project}', 'Kerjakan {project} sebagai karya utuh. Tantang dirimu menambahkan variasi sendiri.',
                ['Kerjakan {project} secara penuh', 'Tambahkan satu variasi gayamu sendiri', 'Evaluasi hasil di tengah jalan']],
        ],
        'polish' => [
            'beginner' => ['Rapikan hasilnya', 'Waktunya penyelesaian: {finish}. Tidak harus sempurna.',
                ['Lakukan ini: {finish}', 'Lihat hasilnya dari jauh dan dari dekat', 'Hargai usahamu sejenak']],
            'intermediate' => ['Penyelesaian yang bersih', 'Fokus pada kerapian: {finish}.',
                ['Lakukan ini: {finish}', 'Periksa detail yang masih kasar', 'Bandingkan dengan hasil terakhirmu']],
            'advanced' => ['Penyelesaian profesional', 'Perhatikan detail akhir: {finish}.',
                ['Lakukan ini: {finish}', 'Periksa konsistensi di seluruh karya', 'Putuskan apa yang akan kamu ubah lain kali']],
        ],
        'mastery' => [
            'beginner' => ['Rayakan dan lanjutkan', 'Kamu sudah membuat sesuatu sendiri. Abadikan {show}, lalu pilih proyek kecil berikutnya.',
                ['Abadikan {show}', 'Tulis satu hal yang kamu pelajari', 'Pilih proyek berikutnya yang terasa menarik']],
            'intermediate' => ['Kembangkan kemampuan', 'Abadikan {show} dan tentukan teknik apa yang ingin kamu dalami berikutnya.',
                ['Abadikan {show}', 'Catat teknik yang ingin kamu dalami', 'Pilih proyek yang sedikit lebih menantang']],
            'advanced' => ['Temukan gayamu', 'Dokumentasikan {show} dan rancang proyek berikutnya dengan gayamu sendiri.',
                ['Dokumentasikan {show}', 'Tuliskan ciri khas yang mulai terbentuk', 'Rancang proyek berikutnya milikmu sendiri']],
        ],
    ];

    /** [nama, kunci alat (cocok dengan jawaban personalisasi) atau null, [harga hemat, standar, premium] dalam ribu rupiah, catatan] */
    private const MATERIALS = [
        'crochet' => [
            'beginner' => [
                ['Hook rajut 3,5 mm', 'yarn_hook', [10, 20, 60], null],
                ['Benang katun 4-ply', null, [15, 25, 45], null],
                ['Gunting kecil', null, [5, 10, 25], null],
                ['Jarum tapestri', null, [3, 5, 15], null],
            ],
            'intermediate' => [
                ['Hook ergonomis 4 mm', 'yarn_hook', [25, 45, 120], null],
                ['Benang katun mercerized', null, [25, 40, 75], null],
                ['Penanda tusuk', null, [5, 10, 30], null],
                ['Gunting dan jarum tapestri', null, [8, 15, 40], null],
            ],
            'advanced' => [
                ['Set hook beberapa ukuran', 'yarn_hook', [60, 120, 300], null],
                ['Benang berkualitas (wol atau katun mercerized)', null, [50, 90, 180], null],
                ['Penanda tusuk dan pita ukur', null, [10, 20, 50], null],
                ['Alas blocking dan jarum pin', null, [30, 60, 150], null],
            ],
        ],
        'painting' => [
            'beginner' => [
                ['Kuas bulat ukuran 6', 'paintbrushes', [10, 25, 70], null],
                ['Cat air set 12 warna', null, [25, 60, 200], null],
                ['Kertas cat air 300 gsm', null, [15, 35, 90], null],
                ['Wadah air dan tisu', null, [3, 5, 10], null],
            ],
            'intermediate' => [
                ['Set kuas bulat dan pipih', 'paintbrushes', [40, 90, 250], null],
                ['Cat air tube', null, [60, 150, 400], null],
                ['Kertas cat air katun', null, [40, 90, 220], null],
                ['Palet dan masking tape', null, [10, 20, 50], null],
            ],
            'advanced' => [
                ['Kuas premium (sintetik atau sable)', 'paintbrushes', [100, 250, 700], null],
                ['Cat air kelas artis', null, [150, 400, 1000], null],
                ['Blok kertas katun 300 gsm', null, [80, 180, 400], null],
                ['Papan lukis atau easel kecil', null, [50, 120, 350], null],
            ],
        ],
        'photography' => [
            'beginner' => [
                ['Kamera', 'camera', [0, 0, 0], 'Ponsel yang kamu punya sudah cukup'],
                ['Aplikasi penyunting foto gratis', null, [0, 0, 0], null],
                ['Buku catatan kecil', null, [5, 10, 30], null],
            ],
            'intermediate' => [
                ['Kamera dengan pengaturan manual', 'camera', [0, 0, 0], 'Ponsel dengan mode manual juga bisa'],
                ['Tripod mini', null, [30, 80, 250], null],
                ['Aplikasi penyunting foto', null, [0, 0, 150], null],
            ],
            'advanced' => [
                ['Kamera dengan lensa yang bisa kamu atur sendiri', 'camera', [0, 0, 0], 'DSLR, mirrorless, atau ponsel kelas atas'],
                ['Tripod kokoh', null, [100, 250, 700], null],
                ['Reflektor lipat', null, [30, 70, 200], null],
                ['Perangkat lunak penyunting', null, [0, 150, 500], null],
            ],
        ],
        'generic' => [
            '*' => [
                ['Bahan utama sesuai panduan proyek', null, [30, 70, 200], null],
                ['Alat dasar', null, [20, 50, 150], null],
                ['Wadah dan tempat penyimpanan', null, [10, 20, 50], null],
            ],
        ],
    ];

    private const TIERS = [
        'budget'   => [0, 'Hemat'],
        'standard' => [1, 'Standar'],
        'premium'  => [2, 'Premium'],
    ];

    public static function rupiah(int $thousands): string
    {
        return $thousands === 0 ? 'Gratis' : 'Rp ' . number_format($thousands * 1000, 0, ',', '.');
    }

    private static function key(Project $project): string
    {
        $project->loadMissing('hobby');
        $slug = $project->hobby?->slug;

        return isset(self::HOBBIES[$slug]) ? $slug : 'generic';
    }

    private static function level(Project $project): string
    {
        return in_array($project->level, Project::LEVELS, true) ? $project->level : 'beginner';
    }

    /** Daftar bahan, ditandai mana yang sudah dimiliki pengguna, dan perkiraan biaya sesuai anggarannya. */
    public static function materialsFor(Project $project, ?User $user = null): array
    {
        $key   = self::key($project);
        $level = self::level($project);
        $set   = self::MATERIALS[$key][$level] ?? self::MATERIALS[$key]['*'] ?? [];

        [$tier, $tierLabel] = self::TIERS[$user?->budget ?? 'standard'] ?? self::TIERS['standard'];
        $owned = $user?->tools ?? [];

        $items = [];
        $total = 0;
        $toolItems = 0;
        $toolOwned = 0;

        foreach ($set as [$name, $tool, $prices, $note]) {
            $has = $tool !== null && in_array($tool, $owned, true);

            if ($tool !== null) {
                $toolItems++;
                $toolOwned += $has ? 1 : 0;
            }

            if (! $has) {
                $total += $prices[$tier];
            }

            $items[] = [
                'name'        => $name,
                'note'        => $note,
                'owned'       => $has,
                'price_label' => self::rupiah($prices[$tier]),
            ];
        }

        return [
            'items'           => $items,
            'total'           => $total,
            'total_label'     => self::rupiah($total),
            'tier_label'      => $tierLabel,
            'all_tools_owned' => $toolItems > 0 && $toolItems === $toolOwned,
        ];
    }

    /** Enam langkah roadmap untuk sebuah proyek. */
    public static function build(Project $project, ?User $user = null): array
    {
        $cfg   = self::HOBBIES[self::key($project)];
        $level = self::level($project);

        $vars = [
            '{project}'  => $project->title,
            '{skill}'    => $project->skill ?: 'teknik utamanya',
            '{noun}'     => $cfg['noun'],
            '{practice}' => $cfg['practice'],
            '{finish}'   => $cfg['finish'],
            '{show}'     => $cfg['show'],
        ];
        $fill = fn (string $text) => strtr($text, $vars);

        $steps = [];

        foreach (self::ORDER as $i => $key) {
            if ($key === 'materials') {
                $mat = self::materialsFor($project, $user);

                $title   = 'Siapkan bahan';
                $summary = $mat['all_tools_owned']
                    ? 'Alat utamamu sudah ada. Periksa bahan yang tersisa, lalu lanjut.'
                    : 'Siapkan alat dan bahan berikut. Tidak harus yang mahal, yang penting cukup untuk mulai.';

                $bullets = array_map(
                    fn ($m) => $m['name'] . ($m['owned'] ? ' (sudah kamu punya)' : ''),
                    $mat['items']
                );
                $bullets[] = $mat['total'] > 0
                    ? "Perkiraan biaya (anggaran {$mat['tier_label']}): {$mat['total_label']}"
                    : 'Tidak ada biaya tambahan yang perlu disiapkan';
            } else {
                [$title, $summary, $bullets] = self::TEXT[$key][$level];
                $title   = $fill($title);
                $summary = $fill($summary);
                $bullets = array_map($fill, $bullets);
            }

            $steps[] = [
                'position' => $i + 1,
                'key'      => $key,
                'label'    => self::LABELS[$key],
                'title'    => $title,
                'summary'  => $summary,
                'bullets'  => $bullets,
                'minutes'  => max(5, (int) (round($project->duration_minutes * self::WEIGHTS[$key] / 5) * 5)),
            ];
        }

        return $steps;
    }
}
