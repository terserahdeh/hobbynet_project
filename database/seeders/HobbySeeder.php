<?php

namespace Database\Seeders;

use App\Models\Hobby;
use Illuminate\Database\Seeder;

class HobbySeeder extends Seeder
{
    public function run(): void
    {
        $hobbies = [
            ['slug' => 'crochet',     'name' => 'Merajut (Crochet)', 'tagline' => 'Dari benang jadi sesuatu yang bisa kamu pegang.',
             'first_project' => 'Alas gelas rajut', 'color' => 'pink', 'sort' => 1],
            ['slug' => 'painting',    'name' => 'Melukis',           'tagline' => 'Warna, air, dan kertas. Itu sudah cukup untuk mulai.',
             'first_project' => 'Kartu pos cat air', 'color' => 'sage', 'sort' => 2],
            ['slug' => 'photography', 'name' => 'Fotografi',         'tagline' => 'Ceritakan harimu lewat ponsel yang sudah kamu punya.',
             'first_project' => 'Cerita foto dengan ponsel', 'color' => 'peri', 'sort' => 3],
        ];

        foreach ($hobbies as $hobby) {
            Hobby::updateOrCreate(['slug' => $hobby['slug']], $hobby);
        }
    }
}
