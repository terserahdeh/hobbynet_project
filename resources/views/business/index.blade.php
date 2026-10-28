@extends('layouts.app')

@section('title', 'Model Bisnis · HobbyNet')

@section('content')
<div class="biz">
    <header class="biz__hero">
        <p class="eyebrow">Model bisnis HobbyNet</p>
        <h1 class="plus__title">Bukan sekadar affiliate:<br>enam sumber pendapatan yang saling menguatkan</h1>
        <p class="plus__lead">HobbyNet membantu pemula menyelesaikan proyek hobi pertamanya. Setiap langkah di perjalanan itu adalah momen yang wajar untuk menghasilkan pendapatan, tanpa memaksa pengguna.</p>
    </header>

    {{-- Alur nilai --}}
    <section class="flow" aria-label="Alur pengguna dan pendapatan">
        <ol class="flow__list">
            <li><b>Mulai</b><span>Pemula memilih hobi dan membuka roadmap</span></li>
            <li><b>Dipandu AI</b><span>Coach AI menjawab saat tersangkut <em>(Plus)</em></span></li>
            <li><b>Belanja pas</b><span>Starter pack mengarah ke toko <em>(affiliate)</em></span></li>
            <li><b>Selesai</b><span>Proyek dibagikan, lencana didapat</span></li>
            <li><b>Naik level</b><span>Kelas, kit, dan komunitas <em>(fase 2 dan 3)</em></span></li>
        </ol>
    </section>

    {{-- Sumber pendapatan --}}
    <section aria-labelledby="h-streams">
        <h2 class="plus__h2" id="h-streams">Sumber pendapatan</h2>
        <div class="streams">
            <article class="stream stream--live">
                <span class="tag tag--live">Sudah ada di aplikasi</span>
                <h3>1. Langganan HobbyNet Plus</h3>
                <p><b>Siapa yang bayar:</b> pengguna. <b>Cara:</b> Coach AI tanpa batas harian, rencana belajar 7 hari. Gratis dibatasi 5 pertanyaan per hari.</p>
                <p class="stream__why">Kenapa kuat: biaya AI naik seiring pemakaian, jadi pengguna berat yang membayar. Margin terjaga.</p>
            </article>
            <article class="stream stream--live">
                <span class="tag tag--live">Sudah ada di aplikasi</span>
                <h3>2. Affiliate starter pack</h3>
                <p><b>Siapa yang bayar:</b> marketplace. <b>Cara:</b> setiap bahan di starter pack punya tautan belanja, HobbyNet menerima komisi per pembelian.</p>
                <p class="stream__why">Kenapa kuat: pengguna sudah niat beli tepat setelah memilih proyek.</p>
            </article>
            <article class="stream">
                <span class="tag">Fase 2</span>
                <h3>3. Paket sponsor dari brand</h3>
                <p><b>Siapa yang bayar:</b> brand bahan kreatif. <b>Cara:</b> slot "paket pilihan" berlabel sponsor di halaman Jelajahi, dengan kurasi dan label yang transparan.</p>
                <p class="stream__why">Kenapa kuat: brand mendapat audiens pemula yang tepat sasaran.</p>
            </article>
            <article class="stream">
                <span class="tag">Fase 2</span>
                <h3>4. Komisi workshop kreator</h3>
                <p><b>Siapa yang bayar:</b> pengguna lewat kreator. <b>Cara:</b> kreator menjual workshop, HobbyNet mengambil persentase tiket.</p>
                <p class="stream__why">Kenapa kuat: pengguna yang menyelesaikan roadmap butuh langkah berikutnya.</p>
            </article>
            <article class="stream">
                <span class="tag">Fase 3</span>
                <h3>5. HobbyNet Starter Kit Box</h3>
                <p><b>Siapa yang bayar:</b> pengguna. <b>Cara:</b> bahan pemula bermerek sendiri yang sudah cocok dengan roadmap.</p>
                <p class="stream__why">Kenapa kuat: margin produk lebih besar dari komisi, dan pengalaman terkontrol.</p>
            </article>
            <article class="stream">
                <span class="tag">Fase 3</span>
                <h3>6. Lisensi komunitas dan kampus</h3>
                <p><b>Siapa yang bayar:</b> organisasi. <b>Cara:</b> langganan per anggota untuk kegiatan kreatif dan dashboard progres.</p>
                <p class="stream__why">Kenapa kuat: pendapatan berulang dan pembelian dalam jumlah banyak.</p>
            </article>
        </div>
    </section>

    {{-- Simulator --}}
    <section class="sim" aria-labelledby="h-sim" id="simulator">
        <div class="sim__head">
            <div>
                <h2 class="plus__h2" id="h-sim">Simulator pendapatan bulanan</h2>
                <p class="sim__note">Semua angka di bawah adalah <b>asumsi yang bisa kamu ubah</b>, bukan data riil. Gunakan untuk menguji skenario dan jelaskan alasannya.</p>
            </div>
            <div class="sim__presets" role="group" aria-label="Skenario">
                <button type="button" class="chipbtn" data-preset="konservatif">Konservatif</button>
                <button type="button" class="chipbtn is-on" data-preset="realistis">Realistis</button>
                <button type="button" class="chipbtn" data-preset="optimis">Optimis</button>
            </div>
        </div>

        <div class="sim__grid">
            <div class="sim__inputs" id="sim-inputs"></div>

            <div class="sim__out" aria-live="polite">
                <div class="kpis">
                    <div class="kpi"><span>Pendapatan / bulan</span><b id="o-total">-</b></div>
                    <div class="kpi"><span>Laba operasional</span><b id="o-profit">-</b></div>
                    <div class="kpi"><span>Margin</span><b id="o-margin">-</b></div>
                    <div class="kpi"><span>Titik impas (pengguna aktif)</span><b id="o-be">-</b></div>
                </div>

                <h3 class="sim__sub">Dari mana uangnya datang</h3>
                <div class="bars" id="bars"></div>

                <h3 class="sim__sub">Ekonomi per pelanggan Plus</h3>
                <ul class="unit">
                    <li><span>Margin kotor per pelanggan / bulan</span><b id="u-margin">-</b></li>
                    <li><span>Perkiraan umur pelanggan (1 / churn)</span><b id="u-life">-</b></li>
                    <li><span>LTV (margin × umur)</span><b id="u-ltv">-</b></li>
                    <li><span>LTV : CAC</span><b id="u-ratio">-</b></li>
                </ul>
                <p class="sim__hint" id="sim-hint"></p>
            </div>
        </div>
    </section>

    {{-- Risiko --}}
    <section aria-labelledby="h-risk">
        <h2 class="plus__h2" id="h-risk">Risiko dan cara kami menjawabnya</h2>
        <div class="risk">
            <div class="risk__row risk__row--head"><span>Risiko</span><span>Mitigasi</span></div>
            <div class="risk__row"><span>Biaya AI membengkak</span><span>Kuota harian di paket gratis, batas pemakaian wajar di Plus, model hemat biaya, dan mode cadangan saat API bermasalah.</span></div>
            <div class="risk__row"><span>Terlalu bergantung pada affiliate</span><span>Affiliate hanya satu dari enam aliran. Langganan dan sponsor tidak bergantung pada kebijakan marketplace.</span></div>
            <div class="risk__row"><span>Churn langganan tinggi</span><span>Nilai Plus terikat ke kemajuan nyata (roadmap, rencana 7 hari), bukan konten yang sekali habis. Paket tahunan menurunkan churn.</span></div>
            <div class="risk__row"><span>Kepercayaan pengguna terhadap rekomendasi</span><span>Konten sponsor selalu berlabel. Starter pack tetap menampilkan opsi esensial dan opsional apa adanya.</span></div>
        </div>
    </section>

    <p class="plus__more">Lihat bagian produknya: <a class="pk-link" href="{{ route('plus') }}">HobbyNet Plus</a>
        @auth · <a class="pk-link" href="{{ route('coach') }}">Coba Coach AI</a>@endauth</p>
</div>
@endsection

@push('scripts')
<script>
(function () {
    // Asumsi awal. Bisa diubah lewat slider atau dipilih dari skenario.
    var FIELDS = [
        { k: 'mau',      label: 'Pengguna aktif per bulan',            min: 500,    max: 100000,  step: 500,   fmt: 'num' },
        { k: 'conv',     label: 'Pengguna gratis yang upgrade ke Plus', min: 0.5,    max: 15,      step: 0.5,   fmt: 'pct' },
        { k: 'price',    label: 'Harga Plus per bulan',                 min: 10000,  max: 100000,  step: 1000,  fmt: 'rp' },
        { k: 'churn',    label: 'Churn Plus per bulan',                 min: 2,      max: 25,      step: 0.5,   fmt: 'pct' },
        { k: 'cac',      label: 'Biaya mendapat 1 pelanggan Plus (CAC)', min: 5000,  max: 150000,  step: 1000,  fmt: 'rp' },
        { k: 'fee',      label: 'Biaya payment gateway',                min: 0,      max: 6,       step: 0.5,   fmt: 'pct' },
        { k: 'buy',      label: 'Pengguna yang beli lewat starter pack', min: 1,     max: 30,      step: 1,     fmt: 'pct' },
        { k: 'basket',   label: 'Rata-rata nilai belanja',              min: 30000,  max: 400000,  step: 5000,  fmt: 'rp' },
        { k: 'comm',     label: 'Komisi affiliate',                     min: 1,      max: 12,      step: 0.5,   fmt: 'pct' },
        { k: 'slots',    label: 'Slot sponsor per bulan',               min: 0,      max: 10,      step: 1,     fmt: 'num' },
        { k: 'slotP',    label: 'Harga per slot sponsor',               min: 500000, max: 10000000, step: 250000, fmt: 'rp' },
        { k: 'people',   label: 'Peserta workshop per bulan',           min: 0,      max: 1000,    step: 10,    fmt: 'num' },
        { k: 'ticket',   label: 'Harga tiket workshop',                 min: 25000,  max: 300000,  step: 5000,  fmt: 'rp' },
        { k: 'take',     label: 'Komisi HobbyNet dari tiket',           min: 5,      max: 35,      step: 1,     fmt: 'pct' },
        { k: 'aiPlus',   label: 'Biaya AI per pelanggan Plus / bulan',  min: 1000,   max: 20000,   step: 500,   fmt: 'rp' },
        { k: 'aiFree',   label: 'Biaya AI per pengguna gratis / bulan', min: 0,      max: 2000,    step: 50,    fmt: 'rp' },
        { k: 'fixed',    label: 'Biaya tetap (server, tim kecil)',      min: 500000, max: 30000000, step: 500000, fmt: 'rp' }
    ];

    var PRESETS = {
        konservatif: { mau: 2000,  conv: 2,   price: 29000, churn: 12,  cac: 90000, fee: 3, buy: 5,  basket: 90000,  comm: 3, slots: 0, slotP: 2000000, people: 0,   ticket: 75000, take: 15, aiPlus: 8000, aiFree: 600, fixed: 1500000 },
        realistis:   { mau: 5000,  conv: 4,   price: 29000, churn: 8,   cac: 60000, fee: 3, buy: 10, basket: 120000, comm: 5, slots: 1, slotP: 2000000, people: 60,  ticket: 75000, take: 20, aiPlus: 6000, aiFree: 500, fixed: 3000000 },
        optimis:     { mau: 20000, conv: 6,   price: 29000, churn: 6,   cac: 45000, fee: 3, buy: 14, basket: 150000, comm: 6, slots: 3, slotP: 3000000, people: 250, ticket: 90000, take: 20, aiPlus: 5000, aiFree: 400, fixed: 8000000 }
    };

    var state = Object.assign({}, PRESETS.realistis);
    var rp = function (n) { return 'Rp ' + Math.round(n).toLocaleString('id-ID'); };
    var num = function (n) { return Math.round(n).toLocaleString('id-ID'); };
    var fmt = { rp: rp, num: num, pct: function (n) { return String(n).replace('.', ',') + '%'; } };

    var wrap = document.getElementById('sim-inputs');
    var inputs = {};

    FIELDS.forEach(function (f) {
        var row = document.createElement('div');
        row.className = 'ctl';
        row.innerHTML = '<label for="f-' + f.k + '"><span>' + f.label + '</span><output id="v-' + f.k + '"></output></label>' +
            '<input id="f-' + f.k + '" type="range" min="' + f.min + '" max="' + f.max + '" step="' + f.step + '">';
        wrap.appendChild(row);
        var el = row.querySelector('input');
        inputs[f.k] = { el: el, out: row.querySelector('output'), f: f };
        el.addEventListener('input', function () {
            state[f.k] = parseFloat(el.value);
            document.querySelectorAll('[data-preset]').forEach(function (b) { b.classList.remove('is-on'); });
            render();
        });
    });

    function sync() {
        FIELDS.forEach(function (f) { inputs[f.k].el.value = state[f.k]; });
    }

    // Rumus dipisah agar mudah diuji.
    function calc(s) {
        var plusUsers = s.mau * s.conv / 100;
        var plusRev = plusUsers * s.price;
        var payFee = plusRev * s.fee / 100;
        var aff = s.mau * (s.buy / 100) * s.basket * (s.comm / 100);
        var spons = s.slots * s.slotP;
        var work = s.people * s.ticket * (s.take / 100);
        var total = plusRev + aff + spons + work;
        var aiCost = plusUsers * s.aiPlus + (s.mau - plusUsers) * s.aiFree;
        var profit = total - payFee - aiCost - s.fixed;

        var perUser = (s.conv / 100) * s.price * (1 - s.fee / 100) + (s.buy / 100) * s.basket * (s.comm / 100)
                    - (s.conv / 100) * s.aiPlus - (1 - s.conv / 100) * s.aiFree;
        var fixedNet = spons + work - s.fixed;
        var breakEven = perUser > 0 ? Math.max(0, -fixedNet / perUser) : null;

        var unitMargin = s.price * (1 - s.fee / 100) - s.aiPlus;
        var life = s.churn > 0 ? 100 / s.churn : 0;
        var ltv = unitMargin * life;

        return {
            streams: [
                { name: 'Langganan Plus', v: plusRev, c: 'a' },
                { name: 'Affiliate starter pack', v: aff, c: 'b' },
                { name: 'Sponsor brand', v: spons, c: 'c' },
                { name: 'Komisi workshop', v: work, c: 'd' }
            ],
            total: total, profit: profit, margin: total > 0 ? profit / total * 100 : 0,
            breakEven: breakEven, unitMargin: unitMargin, life: life, ltv: ltv,
            ratio: s.cac > 0 ? ltv / s.cac : 0, plusUsers: plusUsers
        };
    }
    window.__hobbynetCalc = calc;   // untuk pengujian

    function render() {
        FIELDS.forEach(function (f) { inputs[f.k].out.textContent = fmt[f.fmt](state[f.k]); });
        var r = calc(state);

        document.getElementById('o-total').textContent = rp(r.total);
        var p = document.getElementById('o-profit');
        p.textContent = rp(r.profit); p.className = r.profit >= 0 ? 'is-pos' : 'is-neg';
        document.getElementById('o-margin').textContent = r.margin.toFixed(0) + '%';
        document.getElementById('o-be').textContent = r.breakEven === null ? 'tidak tercapai' : num(r.breakEven);

        var max = Math.max.apply(null, r.streams.map(function (s) { return s.v; })) || 1;
        document.getElementById('bars').innerHTML = r.streams.map(function (s) {
            var share = r.total > 0 ? Math.round(s.v / r.total * 100) : 0;
            return '<div class="bar-row"><span class="bar-row__name">' + s.name + '</span>' +
                '<span class="bar-row__track"><span class="bar-row__fill bar-row__fill--' + s.c + '" style="width:' + Math.max(2, s.v / max * 100) + '%"></span></span>' +
                '<span class="bar-row__val">' + rp(s.v) + ' <small>(' + share + '%)</small></span></div>';
        }).join('');

        document.getElementById('u-margin').textContent = rp(r.unitMargin);
        document.getElementById('u-life').textContent = r.life.toFixed(1).replace('.', ',') + ' bulan';
        document.getElementById('u-ltv').textContent = rp(r.ltv);
        var ratio = document.getElementById('u-ratio');
        ratio.textContent = r.ratio.toFixed(1).replace('.', ',') + 'x';
        ratio.className = r.ratio >= 3 ? 'is-pos' : (r.ratio >= 1 ? '' : 'is-neg');

        var hint = r.ratio >= 3 ? 'LTV : CAC di atas 3x umumnya dianggap sehat untuk bisnis langganan.'
                 : r.ratio >= 1 ? 'LTV masih di atas CAC, tetapi tipis. Tekan churn atau CAC.'
                 : 'LTV di bawah CAC: tiap pelanggan baru merugi. Perbaiki churn, harga, atau cara akuisisi.';
        document.getElementById('sim-hint').textContent = hint;
    }

    document.querySelectorAll('[data-preset]').forEach(function (b) {
        b.addEventListener('click', function () {
            state = Object.assign({}, PRESETS[b.dataset.preset]);
            document.querySelectorAll('[data-preset]').forEach(function (x) { x.classList.toggle('is-on', x === b); });
            sync(); render();
        });
    });

    sync(); render();
})();
</script>
@endpush
