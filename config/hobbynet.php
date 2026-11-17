<?php

return [
    // Tombol "Shop Missing Items": %s diganti nama barang. Ganti ke toko pilihan tim lewat .env.
    'shop_search_url' => env('SHOP_SEARCH_URL', 'https://shopee.co.id/search?keyword=%s'),

    // Kisaran anggaran di personalisasi: perkiraan total bahan wajib untuk SATU proyek.
    // Dikalibrasi dari paket perlengkapan yang ada (umumnya Rp15.000 sampai Rp410.000).
    'budget_tiers' => [
        'budget'   => ['label' => 'Hemat',   'range' => 'Di bawah Rp100.000',    'min' => 0,      'max' => 100000],
        'standard' => ['label' => 'Standar', 'range' => 'Rp100.000 – Rp250.000', 'min' => 100000, 'max' => 250000],
        'premium'  => ['label' => 'Premium', 'range' => 'Di atas Rp250.000',     'min' => 250000, 'max' => null],
    ],
];
