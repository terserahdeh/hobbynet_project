@extends('layouts.app')

@section('title', 'Beranda · Hobby Enabler')

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

        @if ($roadmap)
            @php($p = $roadmap['project'])
            <aside class="rcard" aria-label="Roadmap aktif">
                <div class="rcard__media">
                    @include('partials.project-thumb', ['project' => $p])
                    <span class="rcard__tag"><i class="dot" aria-hidden="true"></i>{{ $p->category }} • {{ $p->level_label }}</span>
                </div>
                <h2 class="rcard__title"><a href="{{ route('projects.show', $p) }}">{{ $p->title }}</a></h2>
                <p class="rcard__now">
                    @if ($roadmap['complete'])
                        Roadmap selesai. Kamu membuatnya!
                    @elseif ($roadmap['done'] === 0)
                        Siap dimulai: Langkah 1 - {{ $roadmap['current']['title'] }}
                    @else
                        Sedang di: Langkah {{ $roadmap['current']['position'] }} - {{ $roadmap['current']['title'] }}
                    @endif
                </p>
                <div class="rcard__foot">
                    @unless ($roadmap['complete'])
                        <span class="rcard__time">
                            <svg viewBox="0 0 24 24" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" aria-hidden="true"><circle cx="12" cy="12" r="9"/><path d="M12 7v5l3 2"/></svg>
                            {{ \App\Models\Project::humanDuration($roadmap['minutes_left']) }} lagi
                        </span>
                        <span class="rcard__live">Jalur belajar aktif</span>
                    @endunless
                </div>
            </aside>
        @endif
    </section>

    <div class="home__grid">

        {{-- ===== Kolom utama ===== --}}
        <div class="home__main">

            @if ($roadmap && ! $roadmap['complete'])
                @php($cp = $roadmap['current'])
                <section aria-labelledby="h-jalan">
                    <div class="section-head">
                        <h2 class="section-title" id="h-jalan">Perjalanan Belajarmu</h2>
                        <span class="section-aside">Checkpoint {{ $cp['position'] }}</span>
                    </div>
                    <article class="journey">
                        <div class="journey__media">
                            @include('partials.project-thumb', ['project' => $roadmap['project']])
                        </div>
                        <div class="journey__body">
                            <span class="eyebrow eyebrow--peri">Checkpoint aktif</span>
                            <h3 class="journey__title">{{ $cp['title'] }}</h3>
                            <p class="journey__desc">{{ $cp['summary'] }}</p>
                            <div class="journey__actions">
                                <a class="btn btn--primary btn--sm"
                                   href="{{ route('projects.step', ['project' => $roadmap['project'], 'position' => $cp['position']]) }}">Mulai Checkpoint</a>
                                <span class="journey__time">
                                    <svg viewBox="0 0 24 24" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" aria-hidden="true"><circle cx="12" cy="12" r="9"/><path d="M12 7v5l3 2"/></svg>
                                    sekitar {{ \App\Models\Project::humanDuration($cp['minutes']) }}
                                </span>
                            </div>
                            <a class="btn btn--primary" href="{{ $go($project) }}">
                                {{ $current['percent'] > 0 ? 'Lanjutkan proyek' : 'Mulai proyek' }}
                            </a>
                        </div>
                    </article>
                </section>
            @endif

            {{-- Daftar proyek (to-do) --}}
            <section aria-labelledby="h-daftar">
                <div class="section-head">
                    <h2 class="section-title" id="h-daftar">Daftar Proyekmu</h2>
                    <span class="section-aside">{{ $todo->count() }} proyek</span>
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
                                        <span class="todo__count">{{ $row->steps_done >= 6 ? 'Selesai' : $row->steps_done . '/6 langkah' }}</span>
                                    </div>
                                </div>
                                <div class="todo__actions">
                                    <a class="link-quiet" href="{{ route('projects.show', $tp) }}">Buka</a>
                                    <form method="POST" action="{{ route('projects.destroy', $tp) }}"
                                          onsubmit="return confirm('Hapus dari daftarmu? Progres proyek ini ikut terhapus.')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="icon-btn" aria-label="Hapus {{ $tp->title }} dari daftar">
                                            <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M4 7h16M10 11v6M14 11v6M6 7l1 12h10l1-12M9 7V4h6v3"/></svg>
                                        </button>
                                    </form>
                                </div>
                            </li>
                        @endforeach
                    </ul>
                @endif
            </section>

            {{-- Populer: sebagian besar dari hobi yang dipilih --}}
            <section id="populer" aria-labelledby="h-populer" data-carousel>
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

                @if ($popular->isEmpty())
                    <div class="empty"><p>Semua proyek populer sudah ada di daftarmu. Hebat!</p></div>
                @else
                    <ul class="tiles tiles--carousel">
                        @foreach ($popular as $item)
                            <li data-item>@include('partials.project-tile', ['project' => $item])</li>
                        @endforeach
                    </ul>
                @endif
            </section>
        </div>

        {{-- ===== Kolom samping ===== --}}
        <aside class="home__side">

            <section aria-labelledby="h-lain">
                <div class="section-head">
                    <h2 class="section-title" id="h-lain">Jelajahi Hobi Lain</h2>
                    <a class="link-quiet" href="{{ route('explore') }}">Lihat semua</a>
                </div>

                @forelse ($otherHobbies as $other)
                    <a class="hrow" href="{{ route('hobbies.show', $other) }}">
                        <span class="hrow__icon thumb--{{ $other->color }}" aria-hidden="true">
                            @include('partials.hobby-icon', ['slug' => $other->slug])
                        </span>
                        <span class="hrow__text">
                            <strong>{{ $other->short_name }}</strong>
                            <small>{{ $other->projects_count }} proyek</small>
                        </span>
                        <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="m9 6 6 6-6 6"/></svg>
                    </a>
                @empty
                    <p class="side-note">Kamu sudah memilih semua hobi yang tersedia.</p>
                @endforelse
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
