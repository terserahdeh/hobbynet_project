# HobbyNet: Monetisasi dan Fitur AI

Dokumen ini ringkasan untuk presentasi mata kuliah Manajemen Produk. Halaman interaktifnya ada di `/bisnis` (simulator pendapatan) dan `/plus` (harga).

## 1. Masalah dan nilai produk
Pemula yang ingin mulai hobi kreatif sering berhenti sebelum proyek pertamanya selesai: bingung memilih bahan, tersangkut di tengah langkah, dan tidak ada yang membimbing. HobbyNet memandu mereka lewat roadmap terpandu, starter pack, dan sekarang Coach AI.

## 2. Enam sumber pendapatan
| # | Sumber | Yang membayar | Status |
|---|--------|---------------|--------|
| 1 | Langganan HobbyNet Plus | Pengguna | Ada di aplikasi |
| 2 | Affiliate starter pack | Marketplace | Ada di aplikasi |
| 3 | Paket sponsor brand (berlabel) | Brand bahan kreatif | Fase 2 |
| 4 | Komisi workshop kreator | Pengguna lewat kreator | Fase 2 |
| 5 | HobbyNet Starter Kit Box | Pengguna | Fase 3 |
| 6 | Lisensi komunitas / kampus | Organisasi | Fase 3 |

**Kenapa bukan hanya affiliate:** komisi affiliate kecil per transaksi dan bergantung pada kebijakan marketplace. Langganan memberi pendapatan berulang, sponsor dan workshop menambah pendapatan yang tidak bergantung pada jumlah pembelian.

## 3. Logika harga Plus
- Gratis: 5 pertanyaan Coach AI per hari. Plus: tanpa batas harian (batas wajar 100).
- Rp 29.000 per bulan, atau Rp 249.000 per tahun (hemat sekitar 28%). Angka ini asumsi dan bisa kamu ubah di `PremiumController::PLANS`.
- Biaya AI naik seiring pemakaian, jadi pengguna berat yang membayar. Itu menjaga margin.

## 4. Alur nilai (flywheel)
Mulai -> dipandu Coach AI saat tersangkut -> belanja bahan lewat starter pack -> selesai dan dapat lencana -> naik level (kelas, kit, komunitas).

## 5. Pertanyaan yang mungkin diajukan dosen
- **"Angka-angkanya dari mana?"** Semua angka di simulator adalah asumsi yang bisa diubah, ditandai jelas sebagai asumsi. Langkah berikutnya: validasi dengan survei calon pengguna dan uji harga (A/B).
- **"Bagaimana kalau biaya AI mahal?"** Kuota harian, batas wajar di Plus, model hemat biaya, dan mode cadangan saat API bermasalah.
- **"Apa bedanya dengan aplikasi lain?"** AI-nya terikat ke posisi pengguna di roadmap (langkah aktif, checklist, level, alat, budget), bukan chatbot umum.
- **"Pembayaran sungguhan?"** Di demo, pembayaran disimulasikan. Di produksi, `PremiumController::subscribe()` diganti webhook Midtrans/Xendit yang mengisi `plus_until`.
- **"Risiko terbesar?"** Churn langganan dan CAC. Simulator menampilkan LTV : CAC supaya terlihat jelas kapan model tidak sehat.

## 6. Fitur AI (Coach AI)
- Halaman `/coach`, tombol melayang di semua halaman, dan kartu di beranda.
- Jawaban dibangun dari konteks: level, alat, budget, target harian, dan langkah roadmap aktif.
- Penyedia: Anthropic (Claude) atau Gemini, dipilih lewat `.env`. Tanpa API key, aplikasi memakai **mode demo** berbasis aturan (tetap memakai konteks pengguna), jadi presentasi tidak mati karena internet atau kuota.
- Fitur Plus: "Rencana belajar 7 hari" (dicek di server, bukan hanya disembunyikan di tampilan).

## 7. Pengaturan
Tambahkan ke `.env` (semua opsional):
```
AI_DRIVER=anthropic        # atau gemini
ANTHROPIC_API_KEY=
ANTHROPIC_MODEL=claude-haiku-5-5
GEMINI_API_KEY=
GEMINI_MODEL=gemini-2.5-flash
```
Lalu jalankan:
```
php artisan migrate
php artisan db:seed --class=RoadmapSeeder   # menambah lencana Plus Member
php artisan storage:link
```
