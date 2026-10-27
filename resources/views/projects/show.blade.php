@extends('layouts.app')

@section('title', $project->title . ' · Hobby Enabler')

@section('content')
<div class="narrow">
    <a class="back" href="{{ $project->hobby ? route('hobbies.show', $project->hobby) : route('explore') }}">
        ← {{ $project->hobby?->short_name ?? 'Jelajahi' }}
    </a>

    @if (session('status'))
        <p class="flash" role="status">{{ session('status') }}</p>
    @endif

    <article class="pdetail">
        <div class="pdetail__media">
            @include('partials.project-thumb', ['project' => $project])
        </div>

        <div class="pdetail__info">
            <span class="tag">{{ $project->category }} • {{ $project->level_label }}</span>
            <h1 class="pdetail__title">{{ $project->title }}</h1>
            <p class="pdetail__desc">{{ $project->description ?? $project->summary }}</p>

            <ul class="facts">
                <li>Sekitar {{ $project->duration_label }}</li>
                <li>{{ $project->learners_label }}</li>
                @if ($project->skill) <li>Teknik utama: {{ $project->skill }}</li> @endif
            </ul>
            <p class="hint">{{ $levelHint }}</p>

            <div class="pdetail__actions">
                @if (! $row)
                    <form method="POST" action="{{ route('projects.add', $project) }}">
                        @csrf
                        <button type="submit" class="btn btn--primary">Tambah ke daftarku</button>
                    </form>
                @else
                    @if ($row->steps_done >= 6)
                        <span class="pill pill--done">Proyek selesai</span>
                    @else
                        <a class="btn btn--primary" href="{{ route('projects.step', ['project' => $project, 'position' => $row->steps_done + 1]) }}">
                            {{ $row->steps_done === 0 ? 'Mulai langkah 1' : 'Lanjutkan langkah ' . ($row->steps_done + 1) }}
                        </a>
                    @endif

                    @if ($row->is_roadmap)
                        <span class="pill">Roadmap utama</span>
                    @else
                        <form method="POST" action="{{ route('projects.roadmap', $project) }}">
                            @csrf
                            <button type="submit" class="btn btn--ghost">Jadikan roadmap utama</button>
                        </form>
                    @endif

                    <form method="POST" action="{{ route('projects.destroy', $project) }}"
                          onsubmit="return confirm('Hapus dari daftarmu? Progres proyek ini ikut terhapus.')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn--quiet-danger">Hapus dari daftar</button>
                    </form>
                @endif
            </div>
        </div>
    </article>

    <section class="block" id="jalur" aria-labelledby="h-jalur">
        <h2 class="section-title" id="h-jalur">Jalur belajar</h2>
        @unless ($row)
            <p class="section-sub">Tambahkan proyek ini ke daftarmu untuk mulai mengikuti langkah-langkahnya.</p>
        @endunless
        <ol class="plan">
            @foreach ($steps as $s)
                <li class="plan__item plan__item--{{ $s['status'] }}">
                    <span class="plan__num">{{ $s['position'] }}</span>
                    <div class="plan__text">
                        @if ($row && $s['status'] !== 'todo')
                            <a class="plan__title" href="{{ route('projects.step', ['project' => $project, 'position' => $s['position']]) }}">{{ $s['title'] }}</a>
                        @else
                            <span class="plan__title">{{ $s['title'] }}</span>
                        @endif
                        <p>{{ $s['summary'] }}</p>
                    </div>
                    <span class="plan__side">
                        {{ \App\Models\Project::humanDuration($s['minutes']) }}
                        @if ($s['status'] === 'done') <em>Selesai</em> @elseif ($s['status'] === 'current') <em>Berikutnya</em> @endif
                    </span>
                </li>
            @endforeach
        </ol>
    </section>

    <section class="block" aria-labelledby="h-bahan">
        <h2 class="section-title" id="h-bahan">Bahan dan alat</h2>
        @include('partials.materials', ['materials' => $materials])
    </section>
</div>
@endsection
