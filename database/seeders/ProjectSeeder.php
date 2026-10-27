<?php

namespace Database\Seeders;

use App\Models\Hobby;
use App\Models\Project;
use Illuminate\Database\Seeder;

class ProjectSeeder extends Seeder
{
    public function run(): void
    {
        if (! Hobby::exists()) {
            $this->call(HobbySeeder::class);
        }

        $hobby = fn (string $slug) => Hobby::where('slug', $slug)->value('id');

        $projects = [
            // Proyek pertama untuk tiga hobi awal
            [
                'hobby_id' => $hobby('crochet'), 'slug' => 'alas-gelas-rajut-katun', 'title' => 'Alas Gelas Rajut Katun',
                'category' => 'Merajut', 'summary' => 'Menguasai rantai dasar dan putaran pertama.',
                'description' => 'Fokus pada pola melingkar hari ini. Kamu makin lancar dengan tusuk tunggal ganda.',
                'duration_minutes' => 50, 'learners_count' => 31000, 'icon' => 'crochet', 'color' => 'pink',
                'is_starter' => true, 'is_popular' => true,
            ],
            [
                'hobby_id' => $hobby('painting'), 'slug' => 'kartu-pos-cat-air', 'title' => 'Kartu Pos Cat Air',
                'category' => 'Melukis', 'summary' => 'Belajar menyapu warna basah dalam satu lembar kecil.',
                'description' => 'Mulai dari dua warna dan satu kuas. Biarkan air yang bekerja.',
                'duration_minutes' => 45, 'learners_count' => 18000, 'icon' => 'painting', 'color' => 'sage',
                'is_starter' => true, 'is_popular' => true,
            ],
            [
                'hobby_id' => $hobby('photography'), 'slug' => 'cerita-foto-sehari-hari', 'title' => 'Cerita Foto Sehari-hari',
                'category' => 'Fotografi', 'summary' => 'Menangkap cahaya dan bayangan di sekitarmu.',
                'description' => 'Susun lima foto dari ponselmu menjadi satu cerita pendek tentang harimu.',
                'duration_minutes' => 120, 'learners_count' => 14000, 'icon' => 'photography', 'color' => 'peri',
                'is_starter' => true, 'is_popular' => true,
            ],

            // Populer dari hobi lain
            [
                'hobby_id' => $hobby('painting'), 'slug' => 'cat-air-botani-zen', 'title' => 'Cat Air Botani yang Menenangkan',
                'category' => 'Melukis', 'summary' => 'Daun dan ranting dengan sapuan lembut.',
                'duration_minutes' => 15, 'learners_count' => 12000, 'icon' => 'painting', 'color' => 'sage', 'is_popular' => true,
            ],
            [
                'hobby_id' => null, 'slug' => 'roti-sourdough-pemula', 'title' => 'Roti Sourdough untuk Pemula',
                'category' => 'Memanggang', 'summary' => 'Dari starter sederhana sampai roti pertamamu.',
                'duration_minutes' => 4320, 'learners_count' => 45000, 'icon' => 'baking', 'color' => 'pink', 'is_popular' => true,
            ],
            [
                'hobby_id' => null, 'slug' => 'jurnal-kulit-klasik', 'title' => 'Jurnal Kulit Klasik',
                'category' => 'Kriya', 'summary' => 'Jilid sampul kulit untuk jurnal harianmu.',
                'duration_minutes' => 120, 'learners_count' => 8000, 'icon' => 'crafting', 'color' => 'peri', 'is_popular' => true,
            ],
            [
                'hobby_id' => null, 'slug' => 'bordir-lingkaran-pemula', 'title' => 'Bordir Lingkaran untuk Pemula',
                'category' => 'Kriya', 'summary' => 'Tiga tusuk dasar di atas kain dalam bingkai.',
                'duration_minutes' => 75, 'learners_count' => 21000, 'icon' => 'crafting', 'color' => 'sage', 'is_popular' => true,
            ],
            [
                'hobby_id' => null, 'slug' => 'pembatas-buku-bunga-kering', 'title' => 'Pembatas Buku Bunga Kering',
                'category' => 'Kriya', 'summary' => 'Mengawetkan bunga kecil menjadi pembatas buku.',
                'duration_minutes' => 40, 'learners_count' => 9500, 'icon' => 'crafting', 'color' => 'pink', 'is_popular' => true,
            ],
            [
                'hobby_id' => null, 'slug' => 'cetak-linocut-sederhana', 'title' => 'Cetak Linocut Sederhana',
                'category' => 'Seni Cetak', 'summary' => 'Ukir satu motif kecil dan cetak berkali-kali.',
                'duration_minutes' => 90, 'learners_count' => 6200, 'icon' => 'crafting', 'color' => 'peri', 'is_popular' => true,
            ],
        ];

        foreach ($projects as $data) {
            Project::updateOrCreate(
                ['slug' => $data['slug']],
                $data + ['image' => 'images/projects/' . $data['slug'] . '.jpg']
            );
        }
    }
}
