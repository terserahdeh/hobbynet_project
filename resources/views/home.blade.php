@extends('layouts.app')

@section('title', 'Beranda · HobbyNet')

@section('content')
@php
    $go = fn ($p) => route('roadmap.show', $p->hobby->slug);
@endphp
<div class="dash">

    {{-- Sapaan + lencana berikutnya --}}
    <section class="dash__head">
        <div>
            <h1 class="dash__title">Siap membuat sesuatu hari ini, {{ $firstName }}?</h1>
            <div class="interests">
                <span class="interests__label" id="minat-label">Minatmu saat ini:</span>
                <ul class="chips" aria-labelledby="minat-label">
                    @forelse ($hobbies as $hobby)
                        <li class="chip chip--{{ $hobby->color }}">
                            <span class="chip__icon" aria-hidden="true">@include('onboarding.partials.hobby-icon', ['slug' => $hobby->slug])</span>
                            {{ $hobby->short_name }}
                        </li>
                    @empty
                        <li class="chip chip--none">Belum ada</li>
                    @endforelse
                </ul>
                <a class="chip-add" href="{{ route('onboarding.hobbies') }}" aria-label="Tambah atau ubah minat">+</a>
            </div>
        </div>

        @if ($nextBadge)
            @php $ratio = min(1, $nextBadge['value'] / $nextBadge['target']); @endphp
            <aside class="badge-card" aria-label="Lencana berikutnya">
                <svg class="ring" viewBox="0 0 36 36" width="44" height="44" role="img"
                     aria-label="{{ $nextBadge['value'] }} dari {{ $nextBadge['target'] }}">
                    <circle cx="18" cy="18" r="15" fill="none" stroke="#EFE3DD" stroke-width="3"/>
                    <circle cx="18" cy="18" r="15" fill="none" stroke="#7E9A6B" stroke-width="3" stroke-linecap="round"
                            stroke-dasharray="{{ round($ratio * 94.25, 2) }} 94.25" transform="rotate(-90 18 18)"/>
                    <circle cx="18" cy="18" r="5" fill="none" stroke="#7E9A6B" stroke-width="1.6"/>
                    <path d="m20.5 15.5-1.4 3.6-3.6 1.4 1.4-3.6z" fill="#7E9A6B"/>
                </svg>
                <div class="badge-card__text">
                    <span class="badge-card__eyebrow">Lencana berikutnya</span>
                    <strong>{{ $nextBadge['name'] }}</strong>
                    <span class="badge-card__hint">{{ $nextBadge['hint'] }} ({{ $nextBadge['value'] }}/{{ $nextBadge['target'] }})</span>
                </div>
            </aside>
        @endif
    </section>

    <div class="dash__grid">

        {{-- Kolom utama: rekomendasi personal --}}
        <div class="dash__main">

            <section aria-labelledby="h-lanjut">
                <h2 class="section-title" id="h-lanjut">Lanjutkan berkarya</h2>

                @if ($current)
                    @php($project = $current['project'])
                    <article class="continue">
                        <div class="continue__media">
                            @include('onboarding.partials.project-thumb', ['project' => $project])
                        </div>
                        <div class="continue__body">
                            <span class="tag">{{ $project->category }} • {{ $project->level_label }}</span>
                            <h3 class="continue__title">{{ $project->title }}</h3>
                            <p class="continue__desc">{{ $project->description ?? $project->summary }}</p>

                            @if ($current['percent'] > 0)
                                <div class="meter">
                                    <span>{{ $current['percent'] }}% selesai</span>
                                    <span class="meter__time">
                                        <svg viewBox="0 0 24 24" width="13" height="13" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" aria-hidden="true"><circle cx="12" cy="12" r="9"/><path d="M12 7v5l3 2"/></svg>
                                        ~{{ $current['time_left'] }} lagi
                                    </span>
                                </div>
                            @else
                                <div class="meter">
                                    <span>Siap dimulai</span>
                                    <span class="meter__time">Sekitar {{ $project->duration_label }}</span>
                                </div>
                            @endif
                            <div class="bar" role="progressbar" aria-valuemin="0" aria-valuemax="100"
                                 aria-valuenow="{{ $current['percent'] }}" aria-label="Kemajuan proyek">
                                <span style="width: {{ $current['percent'] }}%"></span>
                            </div>

                            <a class="btn btn--primary" href="{{ $go($project) }}">
                                {{ $current['percent'] > 0 ? 'Lanjutkan proyek' : 'Mulai proyek' }}
                            </a>
                        </div>
                    </article>
                @else
                    <div class="empty">
                        <p>Belum ada proyek yang berjalan. Pilih hobi dulu, nanti kami siapkan proyek pertamamu.</p>
                        <a class="btn btn--primary" href="{{ route('onboarding.hobbies') }}">Pilih hobi</a>
                    </div>
                @endif
            </section>
        </div>

            <section aria-labelledby="h-hobi">
                <div class="section-head">
                    <h2 class="section-title" id="h-hobi">Hobimu</h2>
                    <a class="link-quiet" href="{{ route('explore') }}">Lihat semua</a>
                </div>

                @if ($hobbyCards->isNotEmpty())
                    <div class="hgrid">
                        @foreach ($hobbyCards as $card)
                            <article class="hcard">
                                <a class="hcard__media" href="{{ route('projects.show', $card['project']) }}" tabindex="-1" aria-hidden="true">
                                    @include('onboarding.partials.project-thumb', ['project' => $card['project']])
                                </a>
                                <h3 class="hcard__title">
                                    <a href="{{ route('projects.show', $card['project']) }}">{{ $card['project']->title }}</a>
                                </h3>
                                <p class="hcard__desc">{{ $card['project']->summary }}</p>
                                <div class="hcard__foot">
                                    <span class="status status--{{ $card['status'] }}">{{ $card['label'] }}</span>
                                    <details class="more">
                                        <summary aria-label="Opsi untuk {{ $card['project']->title }}">…</summary>
                                        <div class="more__menu">
                                            <a href="{{ route('projects.show', $card['project']) }}">Buka proyek</a>
                                        </div>
                                    </details>
                                </div>
                            </article>
                        @endforeach
                    </div>
                @else
                    <div class="empty">
                        <p>Kamu belum memilih hobi. Tidak apa-apa, lihat-lihat dulu saja, atau pilih kapan pun kamu siap.</p>
                        <a class="btn btn--ghost" href="{{ route('onboarding.hobbies') }}">Pilih hobi</a>
                    </div>
                @endif
            </section>
        </div>

        {{-- Kolom samping: populer & dorongan kecil --}}
        <aside class="dash__side">

            <section aria-labelledby="h-populer" data-carousel>
                <div class="section-head">
                    <h2 class="section-title" id="h-populer">Proyek Populer</h2>
                    <div class="arrows">
                        <button type="button" class="arrow" data-prev aria-label="Proyek sebelumnya">
                            <svg viewBox="0 0 24 24" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="m15 6-6 6 6 6"/></svg>
                        </button>
                        <button type="button" class="arrow" data-next aria-label="Proyek berikutnya">
                            <svg viewBox="0 0 24 24" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="m9 6 6 6-6 6"/></svg>
                        </button>
                    </div>
                </div>

                <ul class="plist">
                    @foreach ($popular as $item)
                        <li data-item>
                            <a class="pitem" href="{{ route('projects.show', $item) }}">
                                @include('onboarding.partials.project-thumb', ['project' => $item])
                                <span class="pitem__text">
                                    <span class="pitem__cat">{{ $item->category }}</span>
                                    <strong>{{ $item->title }}</strong>
                                    <span class="pitem__meta">{{ $item->learners_label }} • {{ $item->duration_label }}</span>
                                </span>
                            </a>
                        </li>
                    @endforeach
                </ul>

                <a class="dashed" href="{{ route('explore') }}">Jelajahi semua kategori</a>
            </section>

            <section class="tip" aria-labelledby="h-tip">
                <h2 class="section-title" id="h-tip">Tips Hari Ini</h2>
                <p>“{{ $tip['body'] }}”</p>
                <a href="{{ route('explore') }}">Baca tips lainnya →</a>
            </section>

            <section class="goal" aria-labelledby="h-goal">
                <div class="goal__head">
                    <h2 class="section-title" id="h-goal">Target Harian</h2>
                    <span class="goal__count">{{ $goal['minutes'] }}/{{ $goal['goal'] }} mnt</span>
                </div>
                <div class="bar bar--dark" role="progressbar" aria-valuemin="0" aria-valuemax="100"
                     aria-valuenow="{{ $goal['percent'] }}" aria-label="Target harian">
                    <span style="width: {{ $goal['percent'] }}%"></span>
                </div>
                <p>{{ $goal['message'] }}</p>
            </section>
        </aside>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.querySelectorAll('[data-carousel]').forEach(function (root) {
        var items = [].slice.call(root.querySelectorAll('[data-item]'));
        var prev = root.querySelector('[data-prev]');
        var next = root.querySelector('[data-next]');
        var size = 3, start = 0;

        function render() {
            items.forEach(function (el, i) { el.hidden = !(i >= start && i < start + size); });
            prev.disabled = start === 0;
            next.disabled = start + size >= items.length;
        }

        if (items.length <= size) { prev.hidden = true; next.hidden = true; }

        prev.addEventListener('click', function () { start = Math.max(0, start - size); render(); });
        next.addEventListener('click', function () { start = Math.min(Math.max(0, items.length - size), start + size); render(); });
        render();
    });
</script>
@endpush
