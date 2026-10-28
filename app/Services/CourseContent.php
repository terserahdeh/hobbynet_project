<?php

namespace App\Services;

use App\Models\StarterPack;

class CourseContent
{
    public static function lessons(StarterPack $pack): array
    {
        $category = $pack->project?->icon ?? 'crafting';
        $library = [
            'crochet' => [
                ['Kenalan dengan alat & benang', 'Pilih hakpen yang sesuai label benang. Coba pegang hakpen seperti pensil atau pisau, lalu cari posisi yang nyaman.', 'Siapkan benang katun, hakpen, gunting, dan penanda tusuk. Foto alatmu sebelum memulai.', 'Mengapa ukuran hakpen perlu disesuaikan dengan benang?', 'Supaya ketegangan tusuk stabil dan kain tidak terlalu rapat atau longgar.'],
                ['Simpul awal & rantai dasar', 'Buat slip knot, lalu ulangi yarn over dan tarik melewati loop sebanyak 15–20 kali. Hitung rantai tanpa menarik terlalu kencang.', 'Buat 20 rantai dengan ukuran seragam; ulangi sampai hasilnya rapi.', 'Mengapa rantai awal sering mengerut?', 'Tarikan benang terlalu kencang; kendurkan genggaman.'],
                ['Tusuk tunggal yang konsisten', 'Masukkan hakpen ke rantai kedua, tarik benang, yarn over, tarik melewati dua loop. Ulangi sampai ujung baris.', 'Buat sampel 5 baris tusuk tunggal dan hitung tusuk setiap baris.', 'Apa yang terjadi jika tusuk di ujung baris terlewat?', 'Lebar rajutan akan berkurang.'],
                ['Bentuk dan rapikan karya', 'Ikuti pola proyek, hitung tusuk per putaran, sembunyikan ujung benang dengan jarum tapestry dan rapikan tepinya.', 'Selesaikan karya kecilmu dan cek kerapian pinggirnya.', 'Apa manfaat stitch marker?', 'Menandai awal putaran agar hitungan tidak bergeser.'],
            ],
            'painting' => [
                ['Persiapan cat air dan kertas', 'Gunakan kertas tebal khusus cat air, dua warna, kuas, air bersih, dan tisu. Buat area kerja tidak licin.', 'Siapkan palet dan buat tes warna di kertas sisa.', 'Mengapa kertas tebal diperlukan?', 'Agar tidak mudah melengkung saat terkena air.'],
                ['Teknik basah di atas basah', 'Basahi permukaan kertas tipis, sapukan pigmen, lalu amati gradasinya saat pigmen menyebar.', 'Buat tiga kotak uji gradasi warna.', 'Mengapa warna menyebar?', 'Air membawa pigmen pada permukaan kertas basah.'],
                ['Komposisi dan lapisan', 'Buat sketsa sangat tipis, cat bagian terang dulu, tunggu kering, lalu tambah detail secara bertahap.', 'Lukis kartu pos dengan objek utama dan latar sederhana.', 'Mengapa menunggu lapisan kering?', 'Agar warna tidak bercampur secara tidak sengaja.'],
                ['Finishing', 'Periksa kontras, keringkan karya, rapikan tepian, lalu dokumentasikan dengan cahaya alami.', 'Foto kartu pos sebelum dan setelah sentuhan akhir.', 'Apa fungsi area kosong?', 'Memberi ruang visual agar fokus karya jelas.'],
            ],
            'photography' => [
                ['Kenali cahaya', 'Amati arah cahaya jendela, lampu, atau matahari. Hindari cahaya keras langsung pada wajah jika ingin bayangan lembut.', 'Foto satu objek dari tiga arah pencahayaan.', 'Apa peran cahaya samping?', 'Menonjolkan bentuk dan tekstur.'],
                ['Komposisi', 'Aktifkan grid kamera. Letakkan subjek pada perpotongan garis dan kurangi distraksi di belakangnya.', 'Ambil tiga foto dengan rule of thirds.', 'Mengapa background perlu diperhatikan?', 'Supaya perhatian tidak terpecah dari subjek.'],
                ['Cerita visual', 'Rancang urutan pembuka, detail, aktivitas, hasil, dan penutup yang menunjukkan satu cerita.', 'Pilih lima foto yang saling berhubungan.', 'Apa fungsi foto detail?', 'Memperkaya konteks dan kedekatan cerita.'],
                ['Edit dan kurasi', 'Sesuaikan pencahayaan dan crop secukupnya. Jaga warna konsisten, lalu simpan berkas berkualitas baik.', 'Susun lima foto final dan beri judul cerita.', 'Kenapa konsistensi warna penting?', 'Membuat foto terasa sebagai satu rangkaian.'],
            ],
        ];
        $base = $library[$category] ?? [
            ['Siapkan bahan dan rencana', 'Baca detail dan daftar alat pada halaman paket. Susun bahan esensial dan sediakan ruang kerja yang aman.', 'Buat daftar bahan yang tersedia dan yang harus disiapkan.', 'Apa prioritas persiapan?', 'Bahan utama dan keselamatan kerja.'],
            ['Latihan teknik dasar', 'Lakukan percobaan kecil dengan salah satu teknik proyek. Catat hasil dan bagian yang perlu diperbaiki.', 'Buat satu sampel kecil sebelum produk akhir.', 'Mengapa perlu percobaan?', 'Untuk mengurangi kesalahan pada karya akhir.'],
            ['Kerjakan karya utama', 'Pecah pengerjaan menjadi langkah kecil; cek kualitas dan ketelitian setiap selesai satu tahap.', 'Kerjakan produk utama dan dokumentasikan proses.', 'Bagaimana mengukur progres?', 'Bandingkan hasil tiap tahap dengan rencana awal.'],
            ['Evaluasi dan dokumentasi', 'Periksa hasil, rapikan finishing, foto di tempat terang, dan tulis refleksi sederhana.', 'Ambil foto karya final dan catat tiga pelajaran penting.', 'Mengapa refleksi berguna?', 'Supaya pengerjaan berikutnya lebih efektif.'],
        ];
        return array_map(fn ($lesson, $i) => [
            'number' => $i + 1, 'title' => $lesson[0], 'content' => $lesson[1],
            'task' => $lesson[2], 'question' => $lesson[3], 'answer' => $lesson[4],
        ], $base, array_keys($base));
    }
}
