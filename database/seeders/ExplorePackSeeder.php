<?php

namespace Database\Seeders;

use App\Models\Hobby;
use App\Models\Project;
use App\Models\StarterPack;
use App\Models\StarterPackItem;
use Illuminate\Database\Seeder;

/**
 * Isi halaman Jelajahi: starter pack (perlengkapan + perkiraan harga + tautan Shopee) untuk semua proyek
 * di katalog, ditambah proyek-proyek baru yang hanya ada di Jelajahi.
 *
 * - Paket dicocokkan ke proyek lewat SLUG proyek (lihat ProjectSeeder.php).
 * - Proyek yang sudah punya paket (mis. tiga paket di StarterPackSeeder) dilewati, tidak ditimpa.
 * - Harga hanya perkiraan. Tautan Shopee berupa pencarian dengan nama barang sebagai kata kunci.
 * - Aman dijalankan berulang kali.
 *
 * Jalankan SETELAH ProjectSeeder dan StarterPackSeeder:
 *   php artisan db:seed --class=ExplorePackSeeder
 */
class ExplorePackSeeder extends Seeder
{
    public function run(): void
    {
        $hobbyIds = Hobby::pluck('id', 'slug');

        foreach ($this->newProjects() as $slug => $p) {
            Project::updateOrCreate(['slug' => $slug], [
                'hobby_id'         => $p['hobby'] ? ($hobbyIds[$p['hobby']] ?? null) : null,
                'title'            => $p['title'],
                'category'         => $p['category'],
                'summary'          => $p['summary'],
                'skill'            => $p['skill'],
                'description'      => $p['summary'] . ' Kamu akan berlatih ' . $p['skill'] . '.',
                'level'            => $p['level'],
                'duration_minutes' => $p['minutes'],
                'learners_count'   => $p['learners'],
                'image'            => ProjectImageSeeder::forProject($slug) ?? 'images/projects/' . $slug . '.jpg',
                'icon'             => $p['icon'],   // slug hobi, dipakai filter di Jelajahi
                'color'            => $p['color'],
                'is_starter'       => false,
                'is_popular'       => false,        // hanya tampil di Jelajahi
            ]);
        }

        $made = 0;

        foreach ($this->packs() as $projectSlug => $data) {
            $project = Project::where('slug', $projectSlug)->first();

            if (! $project) {
                $this->command?->warn("Dilewati: proyek \"{$projectSlug}\" tidak ditemukan. Jalankan ProjectSeeder dulu.");
                continue;
            }

            $existing = StarterPack::where('project_id', $project->id)->first();

            if ($existing && $existing->slug !== $projectSlug) {
                continue; // sudah punya paket dari seeder lain
            }

            $pack = StarterPack::updateOrCreate(
                ['slug' => $projectSlug],
                ['project_id' => $project->id, 'prep_minutes' => $data['prep'], 'tip' => $data['tip']]
            );

            StarterPackItem::where('starter_pack_id', $pack->id)->delete();

            foreach ($data['items'] as $i => [$name, $description, $price, $essential]) {
                StarterPackItem::create([
                    'starter_pack_id' => $pack->id,
                    'name'            => $name,
                    'description'     => $description,
                    'price'           => $price,
                    'is_essential'    => $essential,
                    'image'           => null, // foto: public/images/packs/{slug}/item-{urutan}.jpg
                    'position'        => $i + 1,
                    'shop_url'        => $price > 0 ? 'https://shopee.co.id/search?keyword=' . rawurlencode($name) : null,
                ]);
            }

            $made++;
        }

        $this->command?->info("Paket perlengkapan diproses: {$made}");
    }

    private function newProjects(): array
    {
        return [
            'bunga-rajut-mini' => [
                'hobby' => 'crochet',
                'title' => 'Bunga Rajut Mini',
                'category' => 'Merajut',
                'summary' => 'Bunga kecil untuk hiasan atau jepit rambut.',
                'skill' => 'rantai dan tusuk tunggal melingkar',
                'level' => 'beginner',
                'minutes' => 30,
                'learners' => 5200,
                'color' => 'pink',
                'icon' => 'crochet',
            ],
            'headband-rajut' => [
                'hobby' => 'crochet',
                'title' => 'Headband Rajut Sederhana',
                'category' => 'Merajut',
                'summary' => 'Pita rambut hangat dengan tepi yang rapi.',
                'skill' => 'tusuk setengah ganda dan tepi rapi',
                'level' => 'intermediate',
                'minutes' => 90,
                'learners' => 3400,
                'color' => 'pink',
                'icon' => 'crochet',
            ],
            'bookmark-cat-air' => [
                'hobby' => 'painting',
                'title' => 'Bookmark Cat Air',
                'category' => 'Melukis',
                'summary' => 'Pembatas buku mungil dengan sapuan warna lembut.',
                'skill' => 'sapuan basah dan gradasi',
                'level' => 'beginner',
                'minutes' => 25,
                'learners' => 6100,
                'color' => 'sage',
                'icon' => 'painting',
            ],
            'kaktus-cat-air' => [
                'hobby' => 'painting',
                'title' => 'Kaktus dan Pot Cat Air',
                'category' => 'Melukis',
                'summary' => 'Kaktus lucu dengan bentuk dasar yang mudah.',
                'skill' => 'bentuk sederhana dan dua lapis warna',
                'level' => 'beginner',
                'minutes' => 30,
                'learners' => 4800,
                'color' => 'sage',
                'icon' => 'painting',
            ],
            'foto-makanan-sederhana' => [
                'hobby' => 'photography',
                'title' => 'Foto Makanan Sederhana',
                'category' => 'Fotografi',
                'summary' => 'Membuat hidangan sehari-hari tampak menggugah selera.',
                'skill' => 'cahaya jendela dan penataan piring',
                'level' => 'beginner',
                'minutes' => 45,
                'learners' => 7300,
                'color' => 'peri',
                'icon' => 'photography',
            ],
            'foto-langit-senja' => [
                'hobby' => 'photography',
                'title' => 'Foto Langit Senja',
                'category' => 'Fotografi',
                'summary' => 'Menangkap warna langit sore dengan ponsel.',
                'skill' => 'eksposur dan komposisi langit',
                'level' => 'beginner',
                'minutes' => 40,
                'learners' => 6600,
                'color' => 'peri',
                'icon' => 'photography',
            ],
            'lilin-aromaterapi-sederhana' => [
                'hobby' => null,
                'title' => 'Lilin Aromaterapi Sederhana',
                'category' => 'Kriya',
                'summary' => 'Lilin wangi buatan sendiri dalam wadah kaca.',
                'skill' => 'melelehkan lilin dan menuang',
                'level' => 'beginner',
                'minutes' => 90,
                'learners' => 9100,
                'color' => 'pink',
                'icon' => 'crafting',
            ],
            'gantungan-kunci-makrame' => [
                'hobby' => null,
                'title' => 'Gantungan Kunci Makrame',
                'category' => 'Kriya',
                'summary' => 'Simpul dasar makrame jadi gantungan kunci.',
                'skill' => 'simpul makrame dasar',
                'level' => 'beginner',
                'minutes' => 45,
                'learners' => 8300,
                'color' => 'sage',
                'icon' => 'crafting',
            ],
            'tas-tote-jahit-tangan' => [
                'hobby' => null,
                'title' => 'Tas Tote Jahit Tangan',
                'category' => 'Menjahit',
                'summary' => 'Tas kain sederhana tanpa mesin jahit.',
                'skill' => 'jahitan tangan yang rapi',
                'level' => 'intermediate',
                'minutes' => 120,
                'learners' => 4100,
                'color' => 'peri',
                'icon' => 'crafting',
            ],
            'kokedama-tanaman-mini' => [
                'hobby' => null,
                'title' => 'Kokedama Tanaman Mini',
                'category' => 'Berkebun',
                'summary' => 'Tanaman hias dalam bola lumut yang digantung.',
                'skill' => 'membentuk bola tanah dan melilit',
                'level' => 'beginner',
                'minutes' => 60,
                'learners' => 5700,
                'color' => 'sage',
                'icon' => 'crafting',
            ],
        ];
    }

    private function packs(): array
    {
        return [
            'scrunchie-rajut-sederhana' => [
                'prep' => 15,
                'tip' => 'Pilih benang yang lembut dan tidak mudah kusut supaya nyaman di rambut.',
                'items' => [
                    ['Benang katun atau akrilik lembut', 'Lembut di rambut dan mudah dirajut.', 20000, true],
                    ['Hook rajut 4 mm', 'Ukuran nyaman untuk benang sedang.', 20000, true],
                    ['Karet rambut', 'Menjadi inti scrunchie.', 8000, true],
                    ['Gunting kecil', 'Untuk memotong benang.', 10000, true],
                    ['Jarum tapestry', 'Merapikan ujung benang.', 10000, false],
                ],
            ],
            'lap-rajut-persegi' => [
                'prep' => 15,
                'tip' => 'Benang katun 100% lebih awet dicuci berkali-kali.',
                'items' => [
                    ['Benang katun 100%', 'Menyerap air dengan baik.', 25000, true],
                    ['Hook rajut 4–5 mm', 'Ukuran standar untuk pemula.', 20000, true],
                    ['Jarum tapestry', 'Untuk menyembunyikan ujung benang.', 10000, true],
                    ['Gunting kecil', 'Memotong benang dengan bersih.', 10000, true],
                    ['Penghitung baris', 'Membantu mencatat jumlah baris.', 20000, false],
                ],
            ],
            'gantungan-kunci-amigurumi' => [
                'prep' => 20,
                'tip' => 'Rajut dengan tusukan rapat agar isian dakron tidak terlihat dari luar.',
                'items' => [
                    ['Benang katun tipis (milk cotton)', 'Lembut dan rapi untuk bentuk kecil.', 20000, true],
                    ['Hook rajut 2,5 mm', 'Ukuran kecil agar rajutan rapat.', 18000, true],
                    ['Dakron isian', 'Mengisi boneka agar padat dan empuk.', 15000, true],
                    ['Kait gantungan kunci', 'Tempat menyambungkan rajutan ke kunci.', 8000, true],
                    ['Mata boneka (safety eyes)', 'Memberi wajah pada karakter.', 12000, false],
                    ['Penanda jahitan', 'Menandai awal setiap putaran.', 15000, false],
                ],
            ],
            'tas-jaring-belanja' => [
                'prep' => 20,
                'tip' => 'Tali katun yang agak kaku membuat bentuk jaring lebih kokoh.',
                'items' => [
                    ['Benang katun tali (cord)', 'Kuat menahan beban belanjaan.', 45000, true],
                    ['Hook rajut 4–5 mm', 'Cocok untuk benang tebal.', 25000, true],
                    ['Penanda jahitan', 'Menandai sudut dan awal putaran.', 15000, true],
                    ['Jarum tapestry', 'Menyembunyikan ujung benang.', 10000, true],
                    ['Gunting kecil', 'Memotong benang.', 10000, false],
                ],
            ],
            'topi-beanie-rajut' => [
                'prep' => 20,
                'tip' => 'Coba topi di kepala beberapa kali selama mengerjakan, supaya ukurannya pas.',
                'items' => [
                    ['Benang wol atau akrilik worsted', 'Hangat dan elastis.', 50000, true],
                    ['Hook rajut 5 mm', 'Cocok untuk benang worsted.', 25000, true],
                    ['Penanda jahitan', 'Menandai awal putaran.', 15000, true],
                    ['Jarum tapestry', 'Merapikan ujung benang.', 10000, true],
                    ['Pita ukur', 'Mengukur lingkar kepala.', 12000, false],
                ],
            ],
            'syal-pola-kabel' => [
                'prep' => 25,
                'tip' => 'Cuci dan ratakan syal setelah selesai agar pola kabelnya terbuka.',
                'items' => [
                    ['Benang wol atau akrilik worsted', 'Butuh sekitar 300 g untuk satu syal.', 90000, true],
                    ['Hook rajut 5,5 mm', 'Memberi ruang untuk tusuk timbul.', 30000, true],
                    ['Penanda jahitan', 'Menandai awal setiap pola silang.', 15000, true],
                    ['Jarum tapestry', 'Merapikan ujung benang.', 10000, true],
                    ['Jarum kabel atau hook cadangan', 'Menahan jahitan saat disilangkan.', 15000, false],
                    ['Alas blocking dan pin', 'Meratakan hasil akhir.', 60000, false],
                ],
            ],
            'amigurumi-karakter-lengkap' => [
                'prep' => 25,
                'tip' => 'Foto posisi bagian tubuh sebelum dijahit agar proporsinya tetap rapi.',
                'items' => [
                    ['Benang katun 3 warna', 'Untuk badan, wajah, dan detail.', 60000, true],
                    ['Hook rajut 2,5 mm', 'Rajutan rapat tanpa celah.', 18000, true],
                    ['Dakron isian', 'Mengisi bagian tubuh.', 20000, true],
                    ['Mata boneka (safety eyes)', 'Mengunci pada kepala.', 15000, true],
                    ['Jarum tapestry', 'Menjahit bagian tubuh.', 10000, true],
                    ['Penanda jahitan', 'Menandai awal putaran.', 15000, false],
                    ['Pin penjepit', 'Menahan bagian sebelum dijahit.', 12000, false],
                ],
            ],
            'gradasi-langit-senja' => [
                'prep' => 15,
                'tip' => 'Basahi seluruh kertas lebih dulu agar warna menyatu dengan lembut.',
                'items' => [
                    ['Cat air set 12 warna', 'Set pelajar sudah cukup.', 45000, true],
                    ['Kuas bulat no. 8', 'Membawa banyak air untuk sapuan lebar.', 25000, true],
                    ['Kertas cat air 300 gsm', 'Tidak melengkung saat basah.', 30000, true],
                    ['Wadah air', 'Pakai gelas bekas di rumah.', 0, true],
                    ['Selotip kertas', 'Menahan kertas agar rata.', 10000, false],
                    ['Tisu', 'Mengangkat air berlebih.', 5000, false],
                ],
            ],
            'bunga-kecil-dua-warna' => [
                'prep' => 15,
                'tip' => 'Dua warna saja sudah cukup untuk bunga yang manis.',
                'items' => [
                    ['Cat air set 12 warna', 'Pilih dua warna favoritmu.', 45000, true],
                    ['Kuas bulat no. 6', 'Serbaguna untuk kelopak dan daun.', 20000, true],
                    ['Kertas cat air', 'Ukuran A6 sudah pas.', 25000, true],
                    ['Pensil 2B', 'Sketsa tipis sebelum melukis.', 5000, false],
                    ['Palet kecil', 'Mencampur warna.', 12000, false],
                ],
            ],
            'cat-air-botani-zen' => [
                'prep' => 15,
                'tip' => 'Beri jeda tiap lapisan agar daun tampak ringan dan transparan.',
                'items' => [
                    ['Cat air tube warna hijau', 'Beberapa tone hijau untuk daun.', 40000, true],
                    ['Kuas bulat no. 4 dan no. 8', 'Untuk detail dan sapuan lebar.', 30000, true],
                    ['Kertas cat air 300 gsm', 'Tahan basah.', 25000, true],
                    ['Pensil dan penghapus kneaded', 'Sketsa yang mudah dihapus.', 12000, false],
                    ['Palet kecil', 'Mencampur gradasi hijau.', 12000, false],
                ],
            ],
            'lanskap-gunung-berkabut' => [
                'prep' => 20,
                'tip' => 'Biarkan tiap lapisan kering dulu supaya warna tidak keruh.',
                'items' => [
                    ['Set kuas pipih dan bulat', 'Pipih untuk langit, bulat untuk detail.', 60000, true],
                    ['Cat air tube (biru, abu, cokelat)', 'Cukup tiga warna dasar.', 90000, true],
                    ['Kertas cat air katun 300 gsm', 'Menyerap air dengan baik.', 60000, true],
                    ['Selotip kertas', 'Menjaga tepi tetap putih.', 10000, true],
                    ['Palet', 'Mencampur warna.', 15000, false],
                    ['Botol semprot kecil', 'Membasahi kertas dengan merata.', 10000, false],
                ],
            ],
            'stilleven-buah-cat-air' => [
                'prep' => 20,
                'tip' => 'Letakkan buah dekat jendela agar arah cahayanya jelas.',
                'items' => [
                    ['Kuas bulat no. 4 dan no. 8', 'Untuk bentuk dan detail.', 40000, true],
                    ['Cat air set', 'Pilih yang punya merah, kuning, biru, cokelat.', 60000, true],
                    ['Kertas cat air 300 gsm', 'Tahan lapisan berulang.', 40000, true],
                    ['Pensil 2B', 'Sketsa bentuk dasar.', 5000, true],
                    ['Buah segar', 'Ambil dari dapur sebagai model.', 0, true],
                    ['Selotip kertas', 'Menahan kertas.', 10000, false],
                ],
            ],
            'potret-wajah-cat-air' => [
                'prep' => 25,
                'tip' => 'Mulai dengan lapisan sangat tipis, gelapkan sedikit demi sedikit.',
                'items' => [
                    ['Cat air kelas artis', 'Pigmen lebih halus untuk warna kulit.', 180000, true],
                    ['Kuas berkualitas baik (no. 4 dan 8)', 'Ujung runcing untuk detail wajah.', 120000, true],
                    ['Blok kertas katun 300 gsm', 'Tahan banyak lapisan.', 90000, true],
                    ['Pensil 2B dan penghapus', 'Sketsa proporsi wajah.', 15000, true],
                    ['Foto referensi (cetak)', 'Lebih mudah dipelajari daripada layar.', 5000, true],
                    ['Papan alas lukis', 'Menahan kertas tetap rata.', 40000, false],
                ],
            ],
            'kota-hujan-basah' => [
                'prep' => 20,
                'tip' => 'Simpan warna tergelap untuk terakhir agar kontrasnya terasa kuat.',
                'items' => [
                    ['Kuas pipih no. 12 dan bulat no. 6', 'Pipih untuk langit dan jalan.', 60000, true],
                    ['Cat air (biru dingin, abu, oranye)', 'Kontras dingin dan hangat.', 100000, true],
                    ['Kertas cat air katun 300 gsm', 'Tahan basah di atas basah.', 70000, true],
                    ['Selotip kertas', 'Menahan kertas.', 10000, false],
                    ['Botol semprot kecil', 'Membasahi area jalan.', 10000, false],
                ],
            ],
            'potret-cahaya-pagi' => [
                'prep' => 10,
                'tip' => 'Foto di pagi yang berawan memberi cahaya paling lembut.',
                'items' => [
                    ['Ponsel dengan kamera', 'Kemungkinan sudah kamu punya.', 0, true],
                    ['Kain lap lensa', 'Lensa bersih, foto lebih tajam.', 15000, true],
                    ['Objek di dekat jendela', 'Cangkir, tanaman, atau buku.', 0, true],
                    ['Karton putih (pemantul cahaya)', 'Menerangi sisi yang gelap.', 8000, false],
                    ['Buku catatan kecil', 'Mencatat ide dan waktu terbaik.', 15000, false],
                ],
            ],
            'jalan-sore-satu-warna' => [
                'prep' => 10,
                'tip' => 'Pilih satu warna sebelum berjalan, lalu cari di sekitarmu.',
                'items' => [
                    ['Ponsel dengan kamera', 'Cukup untuk memulai.', 0, true],
                    ['Sepatu jalan yang nyaman', 'Pakai yang sudah ada.', 0, true],
                    ['Kain lap lensa', 'Menjaga lensa tetap bersih.', 15000, true],
                    ['Buku catatan kecil', 'Mencatat warna yang kamu temukan.', 15000, false],
                    ['Power bank', 'Menjaga baterai selama berjalan.', 100000, false],
                ],
            ],
            'seri-foto-jalanan' => [
                'prep' => 15,
                'tip' => 'Berdirilah di satu titik dan biarkan orang melintas ke dalam bingkaimu.',
                'items' => [
                    ['Kamera dengan mode manual', 'Atau mode pro di ponsel.', 0, true],
                    ['Kain lap lensa', 'Lensa bersih di jalan berdebu.', 15000, true],
                    ['Kartu memori cadangan', 'Supaya tidak kehabisan ruang.', 60000, false],
                    ['Tali kamera', 'Memudahkan membawa lama-lama.', 35000, false],
                    ['Tas kamera kecil', 'Melindungi dari hujan dan debu.', 120000, false],
                ],
            ],
            'foto-produk-sederhana' => [
                'prep' => 20,
                'tip' => 'Latar putih yang melengkung tanpa lipatan membuat bayangan lebih halus.',
                'items' => [
                    ['Ponsel atau kamera', 'Apa pun yang kamu punya.', 0, true],
                    ['Karton putih besar (latar)', 'Dilengkungkan dari dinding ke meja.', 15000, true],
                    ['Reflektor lipat mini', 'Mengisi bayangan gelap.', 40000, true],
                    ['Tripod mini fleksibel', 'Menjaga posisi tetap sama.', 35000, true],
                    ['Kain lap lensa', 'Lensa bersih.', 15000, true],
                    ['Lampu LED meja', 'Cahaya tambahan saat mendung.', 70000, false],
                ],
            ],
            'esai-foto-naratif' => [
                'prep' => 25,
                'tip' => 'Tulis daftar foto (shot list) dulu, baru mulai memotret.',
                'items' => [
                    ['Kamera dengan lensa yang bisa diatur', 'Atau ponsel kelas atas.', 0, true],
                    ['Buku catatan daftar foto', 'Mencatat alur dan keterangan.', 15000, true],
                    ['Kartu memori cadangan', 'Untuk sesi panjang.', 60000, true],
                    ['Aplikasi penyunting foto', 'Banyak pilihan gratis.', 0, true],
                    ['Tripod kokoh', 'Untuk foto cahaya rendah.', 200000, false],
                    ['Hard disk eksternal', 'Menyimpan seluruh hasil sesi.', 450000, false],
                ],
            ],
            'potret-cahaya-buatan' => [
                'prep' => 25,
                'tip' => 'Geser lampu sedikit demi sedikit. Perubahan kecil, hasil besar.',
                'items' => [
                    ['Lampu LED dengan softbox mini', 'Cahaya utama yang lembut.', 150000, true],
                    ['Tripod lampu', 'Menahan lampu pada tinggi yang tepat.', 90000, true],
                    ['Reflektor lipat', 'Mengisi bayangan di sisi lain.', 40000, true],
                    ['Kain difuser putih', 'Melembutkan cahaya.', 25000, true],
                    ['Latar kain polos', 'Membersihkan latar belakang.', 60000, false],
                    ['Remote shutter', 'Memotret tanpa menggoyang kamera.', 40000, false],
                ],
            ],
            'roti-sourdough-pemula' => [
                'prep' => 30,
                'tip' => 'Sabar dengan starter. Ia butuh beberapa hari untuk aktif.',
                'items' => [
                    ['Tepung terigu protein tinggi', 'Untuk adonan yang kenyal.', 18000, true],
                    ['Starter sourdough', 'Beli jadi, atau buat sendiri dari tepung dan air.', 0, true],
                    ['Timbangan dapur digital', 'Sourdough lebih akurat dengan gram.', 70000, true],
                    ['Wadah kaca besar', 'Untuk fermentasi adonan.', 30000, true],
                    ['Dutch oven atau panci besi', 'Menghasilkan kulit roti renyah.', 250000, false],
                    ['Banneton', 'Membentuk adonan saat fermentasi akhir.', 80000, false],
                    ['Pisau sayat roti (lame)', 'Membuat sayatan di permukaan.', 45000, false],
                ],
            ],
            'bordir-lingkaran-pemula' => [
                'prep' => 15,
                'tip' => 'Kencangkan kain di hoop sampai terasa seperti kulit drum.',
                'items' => [
                    ['Hoop bordir 15 cm', 'Menahan kain tetap kencang.', 15000, true],
                    ['Kain katun polos', 'Mudah ditembus jarum.', 20000, true],
                    ['Benang bordir (set)', 'Pilih beberapa warna dasar.', 25000, true],
                    ['Jarum bordir', 'Lubang jarum lebih besar.', 10000, true],
                    ['Gunting kecil', 'Memotong benang bersih.', 15000, true],
                    ['Pensil kain atau pena hilang', 'Menggambar pola sementara.', 12000, false],
                ],
            ],
            'pembatas-buku-bunga-kering' => [
                'prep' => 20,
                'tip' => 'Tekan bunga selama sekitar dua minggu agar benar-benar kering.',
                'items' => [
                    ['Bunga atau daun segar', 'Pilih yang tipis dan datar.', 0, true],
                    ['Buku tebal sebagai penekan', 'Pakai buku bekas.', 0, true],
                    ['Kertas tisu atau kertas minyak', 'Mengalasi bunga saat ditekan.', 8000, true],
                    ['Plastik laminasi', 'Melindungi pembatas.', 20000, true],
                    ['Lem kertas', 'Menempel bunga.', 10000, true],
                    ['Pita atau tali', 'Hiasan di ujung pembatas.', 8000, false],
                ],
            ],
            'jurnal-kulit-klasik' => [
                'prep' => 30,
                'tip' => 'Lubangi kulit lebih dulu dengan pola yang sama agar jahitan rapi.',
                'items' => [
                    ['Lembaran kulit', 'Kulit asli atau sintetis.', 120000, true],
                    ['Kertas jurnal', 'Isi halaman jurnalmu.', 80000, true],
                    ['Benang lilin', 'Kuat dan tidak mudah putus.', 25000, true],
                    ['Jarum kulit', 'Ujung tumpul untuk jahitan.', 15000, true],
                    ['Pisau potong', 'Memotong kulit dengan bersih.', 40000, true],
                    ['Penggaris besi', 'Menjaga garis tetap lurus.', 25000, true],
                    ['Alas potong', 'Melindungi meja.', 60000, false],
                ],
            ],
            'cetak-linocut-sederhana' => [
                'prep' => 25,
                'tip' => 'Ukir pelan-pelan, selalu arahkan pisau menjauhi tangan.',
                'items' => [
                    ['Lempeng linoleum', 'Lembut dan mudah diukir.', 40000, true],
                    ['Set pisau ukir linocut', 'Beberapa ukuran mata pisau.', 90000, true],
                    ['Tinta cetak blok', 'Berbasis air lebih mudah dibersihkan.', 60000, true],
                    ['Rol karet (brayer)', 'Meratakan tinta.', 70000, true],
                    ['Kertas cetak', 'Kertas tipis dan halus.', 25000, true],
                    ['Sendok kayu', 'Menekan cetakan.', 10000, false],
                ],
            ],
            'bunga-rajut-mini' => [
                'prep' => 15,
                'tip' => 'Pakai sisa benang dari proyek lain juga bisa.',
                'items' => [
                    ['Benang katun warna-warni', 'Beberapa warna untuk kelopak dan tengah.', 25000, true],
                    ['Hook rajut 3 mm', 'Ukuran kecil untuk bentuk mungil.', 18000, true],
                    ['Jarum tapestry', 'Merapikan ujung benang.', 10000, true],
                    ['Gunting kecil', 'Memotong benang.', 10000, false],
                    ['Jepit atau peniti', 'Mengubah bunga jadi aksesori.', 8000, false],
                ],
            ],
            'headband-rajut' => [
                'prep' => 20,
                'tip' => 'Ukur lingkar kepala lebih dulu agar headband pas dan tidak longgar.',
                'items' => [
                    ['Benang akrilik lembut', 'Nyaman di dahi.', 30000, true],
                    ['Hook rajut 4,5 mm', 'Cocok untuk benang sedang.', 22000, true],
                    ['Pita ukur', 'Mengukur lingkar kepala.', 12000, true],
                    ['Jarum tapestry', 'Menyembunyikan ujung benang.', 10000, true],
                    ['Kancing hias', 'Sentuhan akhir yang manis.', 8000, false],
                ],
            ],
            'bookmark-cat-air' => [
                'prep' => 10,
                'tip' => 'Tempel beberapa bookmark sekaligus di satu lembar untuk menghemat kertas.',
                'items' => [
                    ['Cat air set 12 warna', 'Set pelajar cukup.', 45000, true],
                    ['Kuas bulat no. 6', 'Serbaguna.', 20000, true],
                    ['Kertas cat air 300 gsm', 'Tebal agar tidak mudah menekuk.', 25000, true],
                    ['Gunting atau cutter', 'Memotong menjadi bentuk pembatas.', 12000, true],
                    ['Pena garis tipis', 'Menambah detail setelah kering.', 15000, false],
                    ['Tali atau rumbai', 'Hiasan di ujung bookmark.', 6000, false],
                ],
            ],
            'kaktus-cat-air' => [
                'prep' => 15,
                'tip' => 'Mulai dari bentuk dasar: oval untuk kaktus, trapesium untuk pot.',
                'items' => [
                    ['Cat air set 12 warna', 'Butuh hijau, cokelat, dan satu warna cerah.', 45000, true],
                    ['Kuas bulat no. 4 dan no. 6', 'Untuk badan dan duri.', 25000, true],
                    ['Kertas cat air', 'Ukuran A6 atau A5.', 25000, true],
                    ['Pensil 2B', 'Sketsa bentuk dasar.', 5000, true],
                    ['Pena garis tipis', 'Menggambar duri kecil.', 15000, false],
                ],
            ],
            'foto-makanan-sederhana' => [
                'prep' => 15,
                'tip' => 'Dekatkan piring ke jendela. Cahaya samping membuat makanan tampak lebih hidup.',
                'items' => [
                    ['Ponsel dengan kamera', 'Cukup untuk memulai.', 0, true],
                    ['Alas kayu atau kain polos', 'Latar yang bersih dan hangat.', 25000, true],
                    ['Piring atau mangkuk bersih', 'Pakai yang ada di rumah.', 0, true],
                    ['Karton putih (pemantul cahaya)', 'Menerangi sisi yang gelap.', 8000, true],
                    ['Tripod mini fleksibel', 'Foto dari atas jadi stabil.', 35000, false],
                    ['Properti (daun, sendok kayu)', 'Mempercantik tampilan.', 20000, false],
                ],
            ],
            'foto-langit-senja' => [
                'prep' => 10,
                'tip' => 'Datang 15 menit sebelum matahari terbenam. Warna terbaik sering muncul sesudahnya.',
                'items' => [
                    ['Ponsel dengan kamera', 'Cukup untuk memulai.', 0, true],
                    ['Tripod mini fleksibel', 'Menjaga foto tetap stabil saat cahaya redup.', 35000, true],
                    ['Kain lap lensa', 'Lensa bersih.', 15000, true],
                    ['Aplikasi penyunting foto gratis', 'Menyesuaikan warna langit.', 0, false],
                    ['Remote shutter', 'Memotret tanpa menyentuh ponsel.', 40000, false],
                ],
            ],
            'lilin-aromaterapi-sederhana' => [
                'prep' => 30,
                'tip' => 'Tuang lilin pada suhu sekitar 55–60°C agar permukaannya halus.',
                'items' => [
                    ['Wax soya', 'Mudah dibersihkan dan ramah pemula.', 60000, true],
                    ['Sumbu lilin', 'Pilih ukuran sesuai diameter wadah.', 15000, true],
                    ['Minyak esensial atau pewangi lilin', 'Memberi aroma.', 35000, true],
                    ['Wadah kaca tahan panas', 'Tempat menuang lilin.', 25000, true],
                    ['Panci untuk melelehkan wax', 'Pakai peralatan dapur yang ada.', 0, true],
                    ['Termometer dapur', 'Memantau suhu wax.', 40000, false],
                    ['Pewarna lilin', 'Memberi warna lembut.', 20000, false],
                ],
            ],
            'gantungan-kunci-makrame' => [
                'prep' => 15,
                'tip' => 'Tegangkan simpul dengan jari yang sama setiap kali agar bentuknya rata.',
                'items' => [
                    ['Tali makrame katun 3 mm', 'Lembut dan mudah disimpul.', 25000, true],
                    ['Ring gantungan kunci', 'Tempat mengikat tali.', 8000, true],
                    ['Gunting', 'Memotong rumbai.', 10000, true],
                    ['Sisir kecil', 'Merapikan rumbai.', 12000, false],
                    ['Manik kayu', 'Hiasan tambahan.', 15000, false],
                ],
            ],
            'tas-tote-jahit-tangan' => [
                'prep' => 25,
                'tip' => 'Jahit dengan jarak tusuk yang sama dan benang yang tidak terlalu panjang.',
                'items' => [
                    ['Kain kanvas atau drill (1 m)', 'Kuat untuk membawa barang.', 40000, true],
                    ['Benang jahit', 'Pilih warna senada dengan kain.', 12000, true],
                    ['Set jarum jahit tangan', 'Beberapa ukuran.', 10000, true],
                    ['Gunting kain', 'Memotong kain dengan bersih.', 45000, true],
                    ['Pita ukur', 'Mengukur dan menandai pola.', 12000, true],
                    ['Peniti atau klip jahit', 'Menahan kain sebelum dijahit.', 10000, true],
                    ['Pensil kain', 'Menggambar garis jahit.', 10000, false],
                ],
            ],
            'kokedama-tanaman-mini' => [
                'prep' => 20,
                'tip' => 'Pilih tanaman yang tahan lembap, seperti sirih gading.',
                'items' => [
                    ['Tanaman kecil (mis. sirih gading)', 'Mudah dirawat pemula.', 25000, true],
                    ['Campuran tanah liat dan tanah gambut', 'Menjadi bola penahan akar.', 20000, true],
                    ['Lumut sphagnum atau sabut kelapa', 'Membungkus bola tanah.', 15000, true],
                    ['Benang rami atau jute', 'Melilit bola agar tidak pecah.', 12000, true],
                    ['Piring alas', 'Menampung air siraman.', 15000, false],
                    ['Botol semprot', 'Menyiram dengan lembut.', 10000, false],
                ],
            ],
        ];
    }
}
