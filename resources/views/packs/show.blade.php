@extends('layouts.app')

@section('title', $project->title . ' · Paket Persiapan · HobbyNet')

@section('content')
@include('packs._assets')

@php
    $essentials = $pack->essential_items;
    $optionals  = $pack->optional_items;
    $total      = $pack->essential_total;
@endphp

<div class="pk" data-pack="{{ $pack->id }}">
    @include('packs._toast')

    <a href="{{ route('explore') }}" class="pk-back">
        @include('packs._icon', ['name' => 'arrow-left']) Kembali ke Jelajahi
    </a>

    {{-- ===== Kepala paket ===== --}}
    <header class="pk-hero">
        <div class="pk-hero__media">
            @include('packs._thumb', ['color' => $project->color, 'icon' => $project->icon, 'image' => $project->image])
        </div>

        <div>
            <div class="pk-hero__tags">
                <span class="pk-tag pk-tag--{{ $project->color }}">Paket awal proyek</span>
                <span>• Tingkat: {{ $project->level_label }}</span>
                <span>• {{ $pack->is_premium ? '★ Course Plus' : 'Course Gratis' }}</span>
            </div>
            <h1 class="pk-title">{{ $project->title }}</h1>
            <p class="pk-lead">{{ $project->description ?? $project->summary }}</p>

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
                    <p>Daftar perlengkapan untuk proyek ini sedang disiapkan.</p>
                    <a class="btn btn--primary" href="{{ route('courses.show', $pack) }}">Lihat proyek</a>
                </div>
            @endif

            @if ($essentials->isNotEmpty())
                <div class="pk-section-head">
                    <h2 class="pk-h2">Perlengkapan Utama</h2>
                    <span>{{ $essentials->count() }} item dibutuhkan</span>
                </div>
                <p class="pk-hint">Centang bahan yang sudah kamu miliki. Ini hanya checklist persiapan, bukan pembayaran atau transaksi.</p>

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
                <p class="pk-hint">Tidak wajib. Tambahkan nanti kalau kamu sudah merasa nyaman.</p>

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
                <h2 class="pk-h2" id="pk-progress-title">Progres Persiapan</h2>

                <p class="pk-progress__label">
                    <span><span data-owned-count>0</span> dari {{ $essentials->count() }} item utama siap</span>
                    <span data-percent>0%</span>
                </p>
                <div class="bar" role="progressbar" aria-valuemin="0" aria-valuemax="100" aria-valuenow="0"
                     aria-label="Progres persiapan" data-bar-wrap>
                    <span data-bar style="width: 0%"></span>
                </div>

                <dl class="pk-sum">
                    <div>
                        <dt>Perkiraan anggaran</dt>
                        <dd data-total>{{ \App\Models\StarterPack::rupiah($total) }}</dd>
                    </div>
                    <div>
                        <dt>Dihemat (sudah punya)</dt>
                        <dd class="pk-sum__saved" data-saved>− Rp 0</dd>
                    </div>
                    <div class="pk-sum__remain">
                        <dt>Sisa pengeluaran</dt>
                        <dd data-remain>{{ \App\Models\StarterPack::rupiah($total) }}</dd>
                    </div>
                </dl>

                <a href="{{ route('courses.show', $pack) }}" class="btn btn--primary">
                    {{ $pack->is_premium && !auth()->user()->isPlus() ? '★ Buka dengan Plus' : 'Aku Siap — Mulai Course' }} @include('packs._icon', ['name' => 'arrow-right'])
                </a>

                <form method="POST" action="{{ route('bookmarks.toggle', $pack) }}">
                    @csrf
                    <button type="submit" class="btn btn--ghost {{ $isSaved ? 'is-saved' : '' }}">
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

            <section class="pk-tip" style="margin-bottom:18px"><h2 class="pk-h3">Belanja bahan (opsional)</h2><p>Harga hanya estimasi. Jika suatu produk punya tautan toko, kamu bisa melihatnya di marketplace. Pembelian dilakukan langsung di toko, bukan melalui HobbyNet.</p><p style="font-size:12px">Info transparansi: tautan produk saat ini belum terverifikasi sebagai affiliate. Komisi baru dapat diterima setelah bergabung dengan program affiliate dan memakai tautan tracking resmi.</p></section>
            <div class="pk-share">
                <span>Bagikan paket</span>
                <button type="button" class="pk-round" data-copy aria-label="Salin tautan paket">
                    @include('packs._icon', ['name' => 'link'])
                </button>
                <span class="pk-share__done" data-copy-done hidden>Tautan disalin</span>
            </div>
        </aside>
    </div>
</div>
@endsection

@push('scripts')
<script>
(function () {
    var root = document.querySelector('[data-pack]');
    if (!root) return;

    var key   = 'hobbynet:pack:' + root.dataset.pack + ':owned';
    var boxes = Array.prototype.slice.call(root.querySelectorAll('input[data-owned]'));
    var essentials = boxes.filter(function (b) { return b.dataset.essential === '1'; });

    var elCount   = root.querySelector('[data-owned-count]');
    var elPercent = root.querySelector('[data-percent]');
    var elBar     = root.querySelector('[data-bar]');
    var elBarWrap = root.querySelector('[data-bar-wrap]');
    var elTotal   = root.querySelector('[data-total]');
    var elSaved   = root.querySelector('[data-saved]');
    var elRemain  = root.querySelector('[data-remain]');

    function rp(n) { return 'Rp ' + Number(n).toLocaleString('id-ID'); }

    // Pulihkan centangan yang tersimpan di browser ini (hanya kenyamanan, bukan data penting).
    try {
        var stored = JSON.parse(localStorage.getItem(key) || '[]');
        boxes.forEach(function (b) { if (stored.indexOf(b.value) > -1) b.checked = true; });
    } catch (e) {}

    function update() {
        var owned = essentials.filter(function (b) { return b.checked; });
        var total = essentials.reduce(function (s, b) { return s + Number(b.dataset.price); }, 0);
        var saved = owned.reduce(function (s, b) { return s + Number(b.dataset.price); }, 0);
        var pct   = essentials.length ? Math.round(owned.length / essentials.length * 100) : 0;

        elCount.textContent   = owned.length;
        elPercent.textContent = pct + '%';
        elBar.style.width     = pct + '%';
        elBarWrap.setAttribute('aria-valuenow', pct);
        elTotal.textContent   = rp(total);
        elSaved.textContent   = '− ' + rp(saved);
        elRemain.textContent  = rp(total - saved);

        boxes.forEach(function (b) { b.closest('.pk-item').classList.toggle('is-owned', b.checked); });

        try {
            localStorage.setItem(key, JSON.stringify(
                boxes.filter(function (b) { return b.checked; }).map(function (b) { return b.value; })
            ));
        } catch (e) {}
    }

    boxes.forEach(function (b) { b.addEventListener('change', update); });
    update();

    // Salin tautan
    var copyBtn  = root.querySelector('[data-copy]');
    var copyDone = root.querySelector('[data-copy-done]');
    if (copyBtn && navigator.clipboard) {
        copyBtn.addEventListener('click', function () {
            navigator.clipboard.writeText(window.location.href).then(function () {
                copyDone.hidden = false;
                setTimeout(function () { copyDone.hidden = true; }, 2500);
            });
        });
    }
})();
</script>
@endpush
