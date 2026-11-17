
@extends('layouts.app')

@section('title', $project->title . ' · Paket Persiapan · HobbyNet')

@section('content')
@include('packs._assets')

@php
    $essentials = $pack->essential_items;
    $optionals  = $pack->optional_items;
    $total      = $pack->essential_total;

    // Cek akses HobbyNet Plus.
    $isPremium = (bool) $pack->is_premium;
    $hasPlus = auth()->check() && auth()->user()->isPlus();
    $isLocked = $isPremium && ! $hasPlus;

    // Tentukan tujuan tombol.
    $courseUrl = $isLocked
        ? route('plus')
        : route('courses.show', $pack);

    $courseLabel = $isLocked
        ? '★ Upgrade ke HobbyNet Plus'
        : 'Aku Siap — Mulai Proyek';
@endphp

<div class="pk" data-pack="{{ $pack->id }}">
    @include('packs._toast')

    <a href="{{ route('explore') }}" class="pk-back">
        @include('packs._icon', ['name' => 'arrow-left'])
        Kembali ke Jelajahi
    </a>

    {{-- ===== Kepala paket ===== --}}
    <header class="pk-hero">
        <div class="pk-hero__media">
            @include('packs._thumb', [
                'color' => $project->color,
                'icon' => $project->icon,
                'image' => $project->image
            ])
        </div>

        <div>
            <div class="pk-hero__tags">
                <span class="pk-tag pk-tag--{{ $project->color }}">
                    Paket awal proyek
                </span>

                <span>• Tingkat: {{ $project->level_label }}</span>

                <span>
                    • {{ $isPremium ? '★ Course Plus' : 'Course Gratis' }}
                </span>
            </div>

            <h1 class="pk-title">{{ $project->title }}</h1>

            <p class="pk-lead">
                {{ $project->description ?? $project->summary }}
            </p>

            <dl class="pk-facts">
                <div>
                    <dt>Perkiraan anggaran</dt>
                    <dd>{{ $pack->budget_label }}</dd>
                </div>

                <div>
                    <dt>Waktu persiapan</dt>
                    <dd>~{{ $pack->prep_minutes }} menit</dd>
                </div>

                <div>
                    <dt>Lama proyek</dt>
                    <dd>{{ $project->duration_label }}</dd>
                </div>
            </dl>
        </div>
    </header>

    <div class="pk-layout">

        {{-- ===== Daftar perlengkapan ===== --}}
        <div>

            @if ($essentials->isEmpty() && $optionals->isEmpty())
                <div class="empty">
                    <p>
                        Daftar perlengkapan untuk proyek ini sedang disiapkan.
                    </p>

                    <a class="btn btn--primary" href="{{ $courseUrl }}">
                        {{ $isLocked ? '★ Upgrade ke HobbyNet Plus' : 'Lihat proyek' }}
                    </a>
                </div>
            @endif

            @if ($essentials->isNotEmpty())
                <div class="pk-section-head">
                    <h2 class="pk-h2">Perlengkapan Utama</h2>
                    <span>{{ $essentials->count() }} item dibutuhkan</span>
                </div>

                <p class="pk-hint">
                    Centang bahan yang sudah kamu miliki.
                    Ini hanya checklist persiapan, bukan pembayaran atau transaksi.
                </p>

                <ul class="pk-items">
                    @foreach ($essentials as $item)
                        @include('packs._item', ['item' => $item])
                    @endforeach
                </ul>
            @endif

            @if ($optionals->isNotEmpty())
                <div class="pk-section-head">
                    <h2 class="pk-h2">Tambahan Opsional</h2>
                    <span>Boleh dilewati</span>
                </div>

                <p class="pk-hint">
                    Tidak wajib. Tambahkan nanti kalau kamu sudah merasa nyaman.
                </p>

                <ul class="pk-items pk-items--grid">
                    @foreach ($optionals as $item)
                        @include('packs._item', ['item' => $item])
                    @endforeach
                </ul>
            @endif

        </div>

        {{-- ===== Sisi kanan ===== --}}
        <aside class="pk-side">

            <section class="pk-panel" aria-labelledby="pk-progress-title">
                <h2 class="pk-h2" id="pk-progress-title">
                    Progres Persiapan
                </h2>

                <p class="pk-progress__label">
                    <span>
                        <span data-owned-count>0</span>
                        dari {{ $essentials->count() }} item utama siap
                    </span>
                    <span data-percent>0%</span>
                </p>

                <div
                    class="bar"
                    role="progressbar"
                    aria-valuemin="0"
                    aria-valuemax="100"
                    aria-valuenow="0"
                    aria-label="Progres persiapan"
                    data-bar-wrap
                >
                    <span data-bar style="width:0%"></span>
                </div>

                <dl class="pk-sum">
                    <div>
                        <dt>Perkiraan anggaran</dt>
                        <dd data-total>
                            {{ \App\Models\StarterPack::rupiah($total) }}
                        </dd>
                    </div>

                    <div>
                        <dt>Dihemat (sudah punya)</dt>
                        <dd class="pk-sum__saved" data-saved>− Rp 0</dd>
                    </div>

                    <div class="pk-sum__remain">
                        <dt>Sisa pengeluaran</dt>
                        <dd data-remain>
                            {{ \App\Models\StarterPack::rupiah($total) }}
                        </dd>
                    </div>
                </dl>

                {{-- Tombol otomatis mengikuti status Plus --}}
                <a
                    style="color:white"
                    class="btn btn--primary"
                    href="{{ $courseUrl }}"
                >
                    {{ $courseLabel }}
                    @include('packs._icon', ['name' => 'arrow-right'])
                </a>

                @if ($isLocked)
                    <p class="pk-hint" style="margin-top:12px;text-align:center">
                        Proyek ini khusus member HobbyNet Plus.
                        Upgrade untuk membuka panduan dan roadmap lengkapnya.
                    </p>
                @endif

                <form method="POST" action="{{ route('bookmarks.toggle', $pack) }}">
                    @csrf

                    <button
                        type="submit"
                        class="btn btn--ghost {{ $isSaved ? 'is-saved' : '' }}"
                    >
                        @include('packs._icon', ['name' => 'bookmark'])
                        {{ $isSaved ? 'Tersimpan' : 'Simpan untuk Nanti' }}
                    </button>
                </form>
            </section>

            @if ($pack->tip)
                <section class="pk-tip">
                    <h2 class="pk-h3">Butuh bantuan memilih?</h2>
                    <blockquote>“{{ $pack->tip }}”</blockquote>
                </section>
            @endif

            <section class="pk-tip" style="margin-bottom:18px">
                <h2 class="pk-h3">Belanja bahan (opsional)</h2>

                <p>
                    Harga hanya estimasi. Jika suatu produk punya tautan toko,
                    kamu bisa melihatnya di marketplace. Pembelian dilakukan
                    langsung di toko, bukan melalui HobbyNet.
                </p>

                <p style="font-size:12px">
                    Info transparansi: tautan produk saat ini belum terverifikasi
                    sebagai affiliate. Komisi baru dapat diterima setelah
                    bergabung dengan program affiliate dan memakai tautan
                    tracking resmi.
                </p>
            </section>

            <div class="pk-share">
                <span>Bagikan paket</span>

                <button
                    type="button"
                    class="pk-round"
                    data-copy
                    aria-label="Salin tautan paket"
                >
                    @include('packs._icon', ['name' => 'link'])
                </button>

                <span class="pk-share__done" data-copy-done hidden>
                    Tautan disalin
                </span>
            </div>

        </aside>
    </div>
</div>
@endsection

@push('scripts')
<script>
(function () {
    var root = document.querySelector('[data-pack]');
    if (!root) {
        console.warn('Checklist: elemen [data-pack] tidak ditemukan.');
        return;
    }

    var boxes = Array.prototype.slice.call(
        root.querySelectorAll('input[data-owned]')
    );

    var essentials = boxes.filter(function (b) {
        return b.dataset.essential === '1';
    });

    var el = function (sel) {
        return root.querySelector(sel);
    };

    var elCount = el('[data-owned-count]'),
        elPercent = el('[data-percent]'),
        elBar = el('[data-bar]'),
        elBarWrap = el('[data-bar-wrap]'),
        elTotal = el('[data-total]'),
        elSaved = el('[data-saved]'),
        elRemain = el('[data-remain]');

    var tokenMeta = document.querySelector('meta[name="csrf-token"]');
    var token = tokenMeta ? tokenMeta.content : '{{ csrf_token() }}';

    function rp(n) {
        return 'Rp ' + Number(n).toLocaleString('id-ID');
    }

    function set(node, text) {
        if (node) node.textContent = text;
    }

    function update() {
        var owned = essentials.filter(function (b) {
            return b.checked;
        });

        var total = essentials.reduce(function (s, b) {
            return s + Number(b.dataset.price || 0);
        }, 0);

        var saved = owned.reduce(function (s, b) {
            return s + Number(b.dataset.price || 0);
        }, 0);

        var pct = essentials.length
            ? Math.round(owned.length / essentials.length * 100)
            : 0;

        set(elCount, owned.length);
        set(elPercent, pct + '%');

        if (elBar) elBar.style.width = pct + '%';
        if (elBarWrap) elBarWrap.setAttribute('aria-valuenow', pct);

        set(elTotal, rp(total));
        set(elSaved, '− ' + rp(saved));
        set(elRemain, rp(total - saved));

        boxes.forEach(function (b) {
            var item = b.closest('.pk-item');

            if (item) {
                item.classList.toggle('is-owned', b.checked);
            }
        });
    }

    boxes.forEach(function (b) {
        b.addEventListener('change', function () {
            var wanted = b.checked;
            update();

            if (!b.dataset.url) {
                console.error(
                    'Checklist: data-url kosong. Cek packs/_item.blade.php.'
                );
                return;
            }

            fetch(b.dataset.url, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': token,
                    'Accept': 'application/json'
                },
                credentials: 'same-origin'
            })
            .then(function (r) {
                if (!r.ok) throw new Error('HTTP ' + r.status);
                return r.json();
            })
            .then(function (d) {
                b.checked = d.owned;
                update();
            })
            .catch(function (err) {
                console.error(
                    'Gagal menyimpan centang:',
                    b.dataset.url,
                    err
                );

                b.checked = !wanted;
                update();

                alert(
                    'Centang belum tersimpan. Muat ulang halaman lalu coba lagi.'
                );
            });
        });
    });

    update();
    console.info('Checklist siap: ' + boxes.length + ' item.');

    // Salin tautan paket.
    var copyBtn = el('[data-copy]');
    var copyDone = el('[data-copy-done]');

    if (copyBtn && navigator.clipboard) {
        copyBtn.addEventListener('click', function () {
            navigator.clipboard.writeText(window.location.href)
                .then(function () {
                    if (copyDone) {
                        copyDone.hidden = false;

                        setTimeout(function () {
                            copyDone.hidden = true;
                        }, 2500);
                    }
                });
        });
    }
})();
</script>
@endpush
