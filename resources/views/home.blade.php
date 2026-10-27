@extends('layouts.app')

@section('title', 'Beranda · HobbyNet')
@section('title', 'Beranda · HobbyNet')

@section('content')
<div class="home">

    @if (session('status'))
        <p class="flash" role="status">{{ session('status') }}</p>
    @endif

    {{-- ===== Hero: roadmap ===== --}}
    <section class="hero" aria-labelledby="h-hero">
        <div class="hero__main">
            <h1 class="hero__title" id="h-hero">Siap membuat sesuatu milikmu sendiri, {{ $firstName }}?</h1>
            <p class="hero__lead">Ikuti jalur belajarmu, ambil langkah kecil, dan ubah rasa penasaran menjadi karya nyata.</p>

            @if ($roadmap)
                @include('partials.roadmap-stepper', ['steps' => $roadmap['steps']])

                <div class="hero__actions">
                    @if ($roadmap['complete'])
                        <a class="btn btn--primary" href="#populer">Pilih Proyek Berikutnya</a>
                    @else
                        <a class="btn btn--primary"
                           href="{{ route('projects.step', ['project' => $roadmap['project'], 'position' => $roadmap['current']['position']]) }}">
                            {{ $roadmap['done'] === 0 ? 'Mulai Roadmap-ku' : 'Lanjutkan Roadmap-ku' }}
                        </a>
                    @endif
                    <div class="hero__progress">
                        <span class="eyebrow">Progres roadmap: {{ $roadmap['done'] }} dari {{ $roadmap['total'] }} selesai</span>
                        <div class="bar" role="progressbar" aria-valuemin="0" aria-valuemax="100"
                             aria-valuenow="{{ $roadmap['percent'] }}" aria-label="Progres roadmap">
                            <span style="width: {{ $roadmap['percent'] }}%"></span>
                        </div>
                    </div>
                </div>
            @else
                <div class="hero__empty">
                    @if ($hobbies->isEmpty())
                        <p>Kamu belum memilih hobi. Jelajahi hobi yang tersedia, tambahkan satu, dan kami siapkan roadmap pertamamu.</p>
                        <a class="btn btn--primary" href="{{ route('explore') }}">Jelajahi hobi</a>
                    @else
                        <p>Belum ada roadmap yang aktif. Pilih satu proyek dari hobimu, tambahkan ke daftar, lalu jadikan roadmap utama.</p>
                        <a class="btn btn--primary" href="#populer">Lihat pilihan proyek</a>
                    @endif
                </div>
            @endif
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
    <div class="dash__grid">

        {{-- Kolom utama: rekomendasi personal --}}
        <div class="dash__main">
        {{-- Kolom utama: rekomendasi personal --}}
        <div class="dash__main">

            <section aria-labelledby="h-lanjut">
                <h2 class="section-title" id="h-lanjut">Lanjutkan berkarya</h2>

                @if ($current)
                    @php($project = $current['project'])
                    <article class="continue">
                        <div class="continue__media">
                            @include('onboarding.partials.project-thumb', ['project' => $project])
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
                @else
                    <div class="empty">
                        <p>Belum ada proyek yang berjalan. Pilih hobi dulu, nanti kami siapkan proyek pertamamu.</p>
                        <a class="btn btn--primary" href="{{ route('onboarding.hobbies') }}">Pilih hobi</a>
                    </div>
                @endif
            </section>

            <section aria-labelledby="h-hobi">
            <section aria-labelledby="h-hobi">
                <div class="section-head">
                    <h2 class="section-title" id="h-hobi">Hobimu</h2>
                    <a class="link-quiet" href="{{ route('explore') }}">Lihat semua</a>
                    <h2 class="section-title" id="h-hobi">Hobimu</h2>
                    <a class="link-quiet" href="{{ route('explore') }}">Lihat semua</a>
                </div>

                @if ($todo->isEmpty())
                    <div class="empty">
                        <p>Daftarmu masih kosong. Buka salah satu proyek di bawah, lalu tekan <strong>Tambah ke daftarku</strong>.</p>
                    </div>
                @else
                    <ul class="todos">
                        @foreach ($todo as $row)
                            @php($tp = $row->project)
                            <li class="todo">
                                <a class="todo__thumb" href="{{ route('projects.show', $tp) }}" tabindex="-1" aria-hidden="true">
                                    @include('partials.project-thumb', ['project' => $tp])
                                </a>
                                <div class="todo__body">
                                    <a class="todo__title" href="{{ route('projects.show', $tp) }}">{{ $tp->title }}</a>
                                    <span class="todo__meta">
                                        {{ $tp->category }} • {{ $tp->level_label }}
                                        @if ($row->is_roadmap) <span class="pill">Roadmap utama</span> @endif
                                    </span>
                                    <div class="todo__bar">
                                        <div class="bar" role="progressbar" aria-valuemin="0" aria-valuemax="6"
                                             aria-valuenow="{{ $row->steps_done }}" aria-label="Langkah selesai">
                                            <span style="width: {{ round($row->steps_done / 6 * 100) }}%"></span>
                                        </div>
                                    </details>
                                    </details>
                                </div>
                            </article>
                            </article>
                        @endforeach
                    </div>
                @else
                    <div class="empty">
                        <p>Kamu belum memilih hobi. Tidak apa-apa, lihat-lihat dulu saja, atau pilih kapan pun kamu siap.</p>
                        <a class="btn btn--ghost" href="{{ route('onboarding.hobbies') }}">Pilih hobi</a>
                    </div>
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
        </div>

        {{-- Kolom samping: populer & dorongan kecil --}}
        <aside class="dash__side">

            <section aria-labelledby="h-populer" data-carousel>
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
                <p class="section-sub">Pilihan untuk levelmu ({{ $levelLabel }}), terutama dari hobi yang kamu pilih.</p>

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
        </div>

            <section class="tip" aria-labelledby="h-tip">
                <h2 class="section-title" id="h-tip">Tips Hari Ini</h2>
                <p>“{{ $tip['body'] }}”</p>
                <a href="{{ route('explore') }}">Baca tips lainnya →</a>
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

            <section class="tip" aria-labelledby="h-tip">
                <h2 class="section-title" id="h-tip">Tips Hari Ini</h2>
                <p>“{{ $tip['body'] }}”</p>
                @if (! empty($tip['more']))
                    <details class="tip__more">
                        <summary>Baca tips lainnya →</summary>
                        @foreach ($tip['more'] as $extra)
                            <p>“{{ $extra }}”</p>
                        @endforeach
                    </details>
                @endif
            </section>

            @if ($nextBadge)
                @php($ratio = min(1, $nextBadge['value'] / $nextBadge['target']))
                <section class="badge-card" aria-label="Pencapaian berikutnya">
                    <svg class="ring" viewBox="0 0 36 36" width="52" height="52" role="img"
                         aria-label="{{ $nextBadge['value'] }} dari {{ $nextBadge['target'] }}">
                        <circle cx="18" cy="18" r="15" fill="none" stroke="#EFE3DD" stroke-width="3"/>
                        <circle cx="18" cy="18" r="15" fill="none" stroke="#7E9A6B" stroke-width="3" stroke-linecap="round"
                                stroke-dasharray="{{ round($ratio * 94.25, 2) }} 94.25" transform="rotate(-90 18 18)"/>
                        <circle cx="18" cy="18" r="5" fill="none" stroke="#7E9A6B" stroke-width="1.6"/>
                        <path d="m20.5 15.5-1.4 3.6-3.6 1.4 1.4-3.6z" fill="#7E9A6B"/>
                    </svg>
                    <div class="badge-card__text">
                        <span class="eyebrow">Pencapaian berikutnya</span>
                        <strong>{{ $nextBadge['name'] }}</strong>
                        <span class="badge-card__hint">{{ $nextBadge['hint'] }} ({{ $nextBadge['value'] }}/{{ $nextBadge['target'] }} selesai)</span>
                    </div>
                </section>
            @endif
        </aside>
    </div>

    {{-- ===== Tutorial dan proyek lainnya ===== --}}
    @if ($more->isNotEmpty())
        <section class="more" aria-labelledby="h-more">
            <div class="section-head">
                <h2 class="section-title" id="h-more">Tutorial dan Proyek Lainnya</h2>
            </div>
            <p class="section-sub">Ingin mencoba hal di luar hobimu? Mulai dari proyek kecil ini.</p>
            <ul class="tiles tiles--grid">
                @foreach ($more as $item)
                    <li>@include('partials.project-tile', ['project' => $item])</li>
                @endforeach
            </ul>
        </section>
    @endif

    <div class="home__cta">
        <a class="btn-explore" href="{{ route('explore') }}">
            <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="12" cy="12" r="9"/><circle cx="8" cy="11" r="1"/><circle cx="12" cy="7.5" r="1"/><circle cx="16" cy="11" r="1"/><path d="M13 21a2 2 0 0 1 0-4h1.5a2 2 0 0 0 0-4"/></svg>
            Jelajahi semua kategori
        </a>
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
