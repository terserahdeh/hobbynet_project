<?php

namespace Database\Seeders;

use App\Models\Project;
use App\Models\StarterPack;
use App\Models\StarterPackItem;
use Illuminate\Database\Seeder;

/**
 * Starter pack untuk tiga proyek pertama (Merajut, Melukis, Fotografi).
 * Setiap paket dicocokkan ke Project yang SUDAH ADA lewat kata kunci judul.
 * Kalau judul proyekmu berbeda, ubah daftar 'match' di bawah.
 * Harga hanya perkiraan. Aman dijalankan berulang kali.
 *
 * Jalankan: php artisan db:seed --class=StarterPackSeeder
 */
class StarterPackSeeder extends Seeder
{
    public function run(): void
    {
        foreach ($this->packs() as $data) {
            $project = Project::query()
                ->where(function ($q) use ($data) {
                    foreach ($data['match'] as $keyword) {
                        $q->orWhere('title', 'like', "%{$keyword}%");
                    }
                })
                ->first();

            if (! $project) {
                $this->command?->warn("Dilewati: tidak ada Project dengan judul mengandung ["
                    . implode(' / ', $data['match']) . "] untuk paket '{$data['slug']}'.");
                continue;
            }

            $pack = StarterPack::updateOrCreate(
                ['slug' => $data['slug']],
                [
                    'project_id'   => $project->id,
                    'prep_minutes' => $data['prep_minutes'],
                    'tip'          => $data['tip'],
                ]
            );

            StarterPackItem::where('starter_pack_id', $pack->id)->delete();

            foreach ($data['items'] as $i => $item) {
                $position = $i + 1;

                StarterPackItem::create($item + [
                    'starter_pack_id' => $pack->id,
                    'position'        => $position,
                    // Taruh foto di public/images/packs/{slug}/item-{urutan}.jpg
                    'image'           => null,
                    // Tautan pencarian toko online. Ganti dengan tautan produk pilihanmu jika perlu.
                    'shop_url'        => $item['price'] > 0
                        ? 'https://shopee.co.id/search?keyword=' . rawurlencode($item['name'])
                        : null,
                ]);
            }

            $this->command?->info("Paket '{$pack->slug}' terhubung ke proyek: {$project->title}");
        }
    }

    private function packs(): array
    {
        return [
            [
                'slug'         => 'alas-gelas-rajut',
                'match'        => ['alas gelas', 'gelas rajut', 'coaster'],
                'prep_minutes' => 20,
                'tip'          => 'Untuk alas gelas pertamamu, pilih benang katun 100%. Tidak semulur akrilik, jadi lebih mudah melihat di mana hook harus masuk.',
                'items'        => [
                    ['name' => 'Benang katun',      'description' => 'Menyerap air dan jahitannya terlihat jelas. Ketebalan sedang (worsted).',    'price' => 25000, 'is_essential' => true],
                    ['name' => 'Hook rajut 4–5 mm', 'description' => 'Ukuran standar untuk pemula. Pilih gagang ergonomis agar nyaman di tangan.', 'price' => 20000, 'is_essential' => true],
                    ['name' => 'Jarum tapestry',    'description' => 'Untuk menyembunyikan ujung benang di akhir pengerjaan.',                    'price' => 10000, 'is_essential' => true],
                    ['name' => 'Penanda jahitan',   'description' => 'Membantu menandai awal setiap putaran.',                                    'price' => 15000, 'is_essential' => false],
                    ['name' => 'Penghitung baris',  'description' => 'Mencatat sudah berapa putaran yang kamu rajut.',                            'price' => 20000, 'is_essential' => false],
                ],
            ],
            [
                'slug'         => 'kartu-pos-cat-air',
                'match'        => ['kartu pos', 'cat air', 'watercolor'],
                'prep_minutes' => 15,
                'tip'          => 'Mulai dengan hanya tiga warna. Kamu akan terkejut berapa banyak warna baru yang bisa lahir dari campurannya.',
                'items'        => [
                    ['name' => 'Cat air set 12 warna',     'description' => 'Set pelajar sudah cukup untuk mulai. Tidak perlu yang mahal.',                 'price' => 45000, 'is_essential' => true],
                    ['name' => 'Kuas bulat no. 6',         'description' => 'Satu kuas serbaguna untuk garis tipis maupun sapuan lebar.',                    'price' => 20000, 'is_essential' => true],
                    ['name' => 'Kertas cat air kartu pos', 'description' => 'Pilih ketebalan 300 gsm supaya tidak melengkung saat basah.',                    'price' => 25000, 'is_essential' => true],
                    ['name' => 'Selotip kertas',           'description' => 'Merekatkan kertas ke meja agar rata dan tepinya tetap putih bersih.',            'price' => 10000, 'is_essential' => false],
                    ['name' => 'Palet kecil',              'description' => 'Untuk mencampur warna. Piring keramik bekas juga bisa.',                         'price' => 12000, 'is_essential' => false],
                ],
            ],
            [
                'slug'         => 'cerita-foto-ponsel',
                'match'        => ['cerita foto', 'foto dengan ponsel', 'photo story'],
                'prep_minutes' => 10,
                'tip'          => 'Dekatkan dirimu ke objek. Foto yang terasa biasa sering jadi menarik hanya dengan melangkah satu kali lebih dekat.',
                'items'        => [
                    ['name' => 'Ponsel dengan kamera',  'description' => 'Kemungkinan besar kamu sudah punya. Bersihkan lensanya sebelum mulai.', 'price' => 0,     'is_essential' => true],
                    ['name' => 'Tripod mini fleksibel', 'description' => 'Membantu foto lebih stabil, terutama saat cahaya redup.',                'price' => 35000, 'is_essential' => true],
                    ['name' => 'Kain lap lensa',        'description' => 'Lensa yang bersih membuat foto langsung terlihat lebih tajam.',          'price' => 15000, 'is_essential' => true],
                    ['name' => 'Reflektor lipat mini',  'description' => 'Memantulkan cahaya agar objek tidak terlalu gelap.',                      'price' => 40000, 'is_essential' => false],
                    ['name' => 'Buku catatan kecil',    'description' => 'Untuk mencatat ide dan urutan ceritamu.',                                'price' => 15000, 'is_essential' => false],
                ],
            ],
        ];
    }
}
