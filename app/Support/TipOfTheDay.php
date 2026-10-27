<?php

namespace App\Support;

use Illuminate\Support\Collection;

class TipOfTheDay
{
    private const TIPS = [
        'crochet' => [
            'Saat proyek rajutmu terasa macet, coba longgarkan genggamanmu. Tegangnya benang sering hanya cerminan fokusmu; biarkan mengalir wajar.',
            'Hitung tusukanmu di akhir tiap putaran. Kebiasaan kecil ini menyelamatkanmu dari bongkar-pasang yang melelahkan.',
        ],
        'painting' => [
            'Basahi kertas lebih dulu, lalu biarkan warna menyebar sendiri. Hasil yang tak terduga sering jadi bagian terbaiknya.',
            'Cat air lebih ramah jika kamu mulai dari warna terang dan menambah gelapnya sedikit demi sedikit.',
        ],
        'photography' => [
            'Berjalanlah sepuluh menit tanpa tujuan dan potret apa pun yang menarik matamu. Cahaya sore membuat benda biasa tampak istimewa.',
            'Dekatkan dirimu ke objek. Foto yang terasa biasa sering jadi menarik hanya dengan melangkah satu kali lebih dekat.',
        ],
        'general' => [
            'Mulai kecil sudah cukup. Lima menit hari ini lebih berarti daripada rencana besar yang belum dimulai.',
        ],
    ];

    /** @return array{body: string, more: array<int, string>} */
    public static function for(Collection $hobbies): array
    {
        $pool = [];

        foreach ($hobbies as $hobby) {
            foreach (self::TIPS[$hobby->slug] ?? [] as $tip) {
                $pool[] = $tip;
            }
        }

        if ($pool === []) {
            $pool = self::TIPS['general'];
        }

        $index = now()->dayOfYear % count($pool);
        $more  = array_values(array_filter($pool, fn ($key) => $key !== $index, ARRAY_FILTER_USE_KEY));

        return ['body' => $pool[$index], 'more' => array_slice($more, 0, 2)];
    }
}
