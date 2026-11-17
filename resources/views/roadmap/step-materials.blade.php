@extends('layouts.app')

@section('title', $step->title)

@section('content')
<link rel="stylesheet" href="{{ asset('css/roadmap.css') }}">
<link rel="stylesheet" href="{{ asset('css/roadmap-materials.css') }}">

@inject('materialsChecklist', 'App\Services\MaterialsChecklist')
@inject('packChecklist', 'App\Services\PackChecklist')

@php
    $state = $materialsChecklist->forStep($step, auth()->user());
    $required = $state['required'];
    $optional = $state['optional'];
    $owned = $state['owned'];
    $stats = $state['stats'];
    $rupiah = fn ($n) => \App\Services\MaterialsChecklist::rupiah($n);
    $pack = $packChecklist->forProject($project, auth()->user())['pack'] ?? null;
    $firstName = \Illuminate\Support\Str::before(auth()->user()->name, ' ');
    $stepUrl = fn ($s) => $s->isShare()
        ? route('roadmap.submission.show', $hobby->slug)
        : route('roadmap.steps.show', [$hobby->slug, $s->slug]);
    $alreadyDone = $step->state === 'completed';
@endphp

<div class="rm-page">
    <header class="rm-topbar">
        <div class="rm-topbar__left">
            <a href="{{ route('roadmap.show', $hobby->slug) }}" class="rm-topbar__close" aria-label="Kembali ke perjalananmu">@include('roadmap.partials.icon', ['name' => 'close'])</a>
            <div class="rm-topbar__title">
                <strong>{{ $project->title }}</strong>
                <span>Guided experience</span>
            </div>
        </div>
        <div class="rm-topbar__mid">
            <div class="rm-topbar__labels">
                <span>Langkah {{ $step->position }} dari {{ $summary['total'] }}</span>
                <span>{{ $summary['percent'] }}% progres roadmap</span>
            </div>
            <div class="rm-bar" role="progressbar" aria-valuenow="{{ $summary['percent'] }}" aria-valuemin="0" aria-valuemax="100">
                <span class="rm-bar__fill" style="width: {{ $summary['percent'] }}%"></span>
            </div>
        </div>
        <div class="rm-topbar__right"><a href="{{ route('roadmap.show', $hobby->slug) }}">Simpan &amp; Keluar</a></div>
    </header>

    <div class="rm-body">
    <div class="rm-container rm-container--wide">
        @include('roadmap.partials.flash')

        <div class="rm-layout">
            <aside>
                <ol class="rm-steplist">
                    @foreach ($steps as $s)
                        <li>
                            @if ($s->state === 'locked')
                                <span class="is-locked">
                                    <span class="rm-step-dot">{{ $s->position }}</span>
                                    <span>{{ $s->position }}. {{ $s->title }}</span>
                                </span>
                            @else
                                <a href="{{ $stepUrl($s) }}"
                                   class="{{ $s->id === $step->id ? 'is-current' : ($s->state === 'completed' ? 'is-done' : '') }}"
                                   @if ($s->id === $step->id) aria-current="step" @endif>
                                    <span class="rm-step-dot">
                                        @if ($s->state === 'completed' && $s->id !== $step->id) @include('roadmap.partials.icon', ['name' => 'check']) @else {{ $s->position }} @endif
                                    </span>
                                    <span>{{ $s->position }}. {{ $s->title }}</span>
                                </a>
                            @endif
                        </li>
                    @endforeach
                </ol>

                @if ($step->encouragement)
                    <div class="rm-hint">
                        <strong>@include('roadmap.partials.icon', ['name' => 'bulb']) Kamu hebat, {{ $firstName }}!</strong>
                        <p>{{ $step->encouragement }}</p>
                        <small>Terus semangat!</small>
                    </div>
                @endif
            </aside>

            <div>
                <main class="rm-card rm-step rmm-card-main">
                    <p class="rm-eyebrow">Langkah instruksi</p>
                    <h1>{{ $step->title }}</h1>
                    <p class="rm-step__meta">
                        <span>@include('roadmap.partials.icon', ['name' => 'clock']) Estimasi waktu: <b>{{ $step->estimated_minutes }} menit</b></span>
                        @if ($step->objective)
                            <i class="rm-step__sep" aria-hidden="true"></i>
                            <span>@include('roadmap.partials.icon', ['name' => 'target']) Tujuan: <b>{{ $step->objective }}</b></span>
                        @endif
                    </p>

                    <div class="rmm-layout">
                        <div>
                            @if ($step->instructions->isNotEmpty())
                                <ul class="rmm-tips" aria-label="Sebelum mulai">
                                    @foreach ($step->instructions as $ins)
                                        <li>@include('roadmap.partials.icon', ['name' => 'bulb'])<span>{{ $ins->body }}</span></li>
                                    @endforeach
                                </ul>
                            @endif

                            <section class="rmm-section" aria-labelledby="rmm-req">
                                <div class="rmm-section__head">
                                    <h2 id="rmm-req">Perlengkapan Utama</h2>
                                    <span class="rmm-count"><b data-rmm="owned">{{ $stats['owned'] }}</b>/{{ $stats['total'] }} sudah dimiliki</span>
                                </div>
                                <ul class="rmm-list">
                                    @foreach ($required as $m)
                                        @include('roadmap.partials.material-row', ['m' => $m])
                                    @endforeach
                                </ul>
                            </section>

                            @if ($optional->isNotEmpty())
                                <section class="rmm-section" aria-labelledby="rmm-opt">
                                    <div class="rmm-section__head">
                                        <h2 id="rmm-opt">Tambahan (Opsional)</h2>
                                        <span class="rmm-count">Tidak wajib</span>
                                    </div>
                                    <ul class="rmm-list rmm-list--optional">
                                        @foreach ($optional as $m)
                                            @include('roadmap.partials.material-row', ['m' => $m])
                                        @endforeach
                                    </ul>
                                </section>
                            @endif
                        </div>

                        <aside class="rmm-side">
                            <section class="rmm-panel rmm-progress" aria-labelledby="rmm-prog">
                                <h2 id="rmm-prog">Progres Persiapan</h2>
                                <div class="rmm-progress__top">
                                    <strong><span data-rmm="percent">{{ $stats['percent'] }}</span>%</strong>
                                    <span><span data-rmm="owned2">{{ $stats['owned'] }}</span> dari {{ $stats['total'] }} item siap</span>
                                </div>
                                <div class="rm-bar" role="progressbar" aria-valuemin="0" aria-valuemax="100" aria-valuenow="{{ $stats['percent'] }}" data-rmm="bar">
                                    <span class="rm-bar__fill" data-rmm="fill" style="width: {{ $stats['percent'] }}%"></span>
                                </div>
                                <dl class="rmm-budget">
                                    <div><dt>Perkiraan total</dt><dd data-rmm="budget">{{ $rupiah($stats['budget']) }}</dd></div>
                                    <div class="rmm-budget__buy"><dt>Perlu dibeli</dt><dd data-rmm="to_buy">{{ $rupiah($stats['to_buy']) }}</dd></div>
                                </dl>

                                <form method="POST" action="{{ route('roadmap.steps.complete', [$hobby->slug, $step->slug]) }}">
                                    @csrf
                                    <button type="submit" id="rmm-ready" class="rm-btn rmm-ready"
                                            @disabled(! $stats['ready'] && ! $alreadyDone)>
                                        {{ $alreadyDone ? 'Lanjut' : 'Aku Siap — Mulai Proyek' }} @include('roadmap.partials.icon', ['name' => 'arrow-right'])
                                    </button>
                                </form>
                                <p class="rmm-note" id="rmm-note" @if ($stats['ready'] || $alreadyDone) hidden @endif>Tandai semua perlengkapan utama sebagai “Sudah punya” untuk lanjut.</p>
                                @error('checklist') <p class="rm-error">{{ $message }}</p> @enderror
                            </section>

                            @if ($pack)
                                <section class="rmm-panel rmm-shop">
                                    <h3>Malas cari satu-satu?</h3>
                                    <p>Beli semuanya sekaligus lewat paket starter.</p>
                                    <a href="{{ route('packs.show', $pack) }}" class="rmm-shop__link">Lihat paket starter @include('roadmap.partials.icon', ['name' => 'chev-right'])</a>
                                </section>
                            @endif
                        </aside>
                    </div>
                </main>

                <div class="rm-foot">
                    @if ($previous)
                        <a href="{{ $stepUrl($previous) }}" class="rm-link">@include('roadmap.partials.icon', ['name' => 'chev-left']) Langkah sebelumnya</a>
                    @else
                        <a href="{{ route('roadmap.show', $hobby->slug) }}" class="rm-link">@include('roadmap.partials.icon', ['name' => 'chev-left']) Kembali ke Roadmap</a>
                    @endif
                </div>
            </div>
        </div>
    </div>
    </div>
</div>

<script>
    (function () {
        var meta = document.querySelector('meta[name="csrf-token"]');
        var token = meta ? meta.content : '{{ csrf_token() }}';
        var alreadyDone = {{ $alreadyDone ? 'true' : 'false' }};
        var btn = document.getElementById('rmm-ready');
        var note = document.getElementById('rmm-note');
        function $(k) { return document.querySelector('[data-rmm="' + k + '"]'); }

        function paint(s) {
            $('owned').textContent = s.owned;
            $('owned2').textContent = s.owned;
            $('percent').textContent = s.percent;
            $('fill').style.width = s.percent + '%';
            $('bar').setAttribute('aria-valuenow', s.percent);
            $('budget').textContent = s.budget_label;
            $('to_buy').textContent = s.to_buy_label;
            if (!alreadyDone) { btn.disabled = !s.ready; note.hidden = s.ready; }
        }

        document.querySelectorAll('.rmm-own input[type=checkbox]').forEach(function (box) {
            box.addEventListener('change', function () {
                var wanted = box.checked;
                var row = box.closest('.rmm-item');
                row.classList.toggle('is-owned', wanted);
                fetch(box.dataset.url, {
                    method: 'PATCH',
                    headers: { 'X-CSRF-TOKEN': token, 'Accept': 'application/json' }
                }).then(function (r) { if (!r.ok) throw new Error(); return r.json(); })
                  .then(function (d) { box.checked = d.owned; row.classList.toggle('is-owned', d.owned); paint(d.stats); })
                  .catch(function () { box.checked = !wanted; row.classList.toggle('is-owned', !wanted); });
            });
        });
    })();
</script>
@endsection
