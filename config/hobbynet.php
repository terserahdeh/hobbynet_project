<?php

return [
    // Tombol "Shop Missing Items": %s diganti nama barang. Ganti ke toko pilihan tim lewat .env.
    'shop_search_url' => env('SHOP_SEARCH_URL', 'https://shopee.co.id/search?keyword=%s'),
];
