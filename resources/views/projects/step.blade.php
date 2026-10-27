@extends('layouts.app')

@section('title', $step['title'] . ' · ' . $project->title)

@section('content')
<div class="narrow">
    <a class="back" href="{{ route('projects.show', $project) }}">← {{ $project->title }}</a>

    @if (session('status'))
        <p class="flash" role="status">{{ session('status') }}</p>
    @endif

    <div class="stepcard-wrap">
        @include('partials.roadmap-stepper', ['steps' => $steps, 'link' => true, 'project' => $project])
    </div>

    <article class="stepcard">
        <span class="eyebrow">Langkah {{ $step['position'] }} dari 6 • sekitar {{ \App\Models\Project::humanDuration($step['minutes']) }}</span>
        <h1 class="stepcard__title">{{ $step['title'] }}</h1>
        <p class="stepcard__lead">{{ $step['summary'] }}</p>

        <h2 class="stepcard__sub">Yang akan kamu lakukan</h2>
        <ul class="checks">
            @foreach ($step['bullets'] as $bullet)
                <li>{{ $bullet }}</li>
            @endforeach
        </ul>

        @if ($step['key'] === 'materials')
            <h2 class="stepcard__sub">Bahan dan alat</h2>
            @include('partials.materials', ['materials' => $materials])
        @endif

        <div class="stepcard__actions">
            @if ($step['status'] === 'current')
                <form method="POST" action="{{ route('projects.step.complete', ['project' => $project, 'position' => $step['position']]) }}">
                    @csrf
                    <button type="submit" class="btn btn--primary">
                        {{ $step['position'] === 6 ? 'Selesai, rayakan!' : 'Selesai, lanjut' }}
                    </button>
                </form>
                <p class="stepcard__hint">Tekan tombol ini kalau langkah ini sudah kamu kerjakan. Tidak perlu terburu-buru.</p>
            @else
                <span class="pill pill--done">Langkah ini sudah selesai</span>
            @endif
        </div>

        <nav class="stepnav" aria-label="Pindah langkah">
            @if ($step['position'] > 1)
                <a href="{{ route('projects.step', ['project' => $project, 'position' => $step['position'] - 1]) }}">← Langkah sebelumnya</a>
            @else
                <span></span>
            @endif
            @if ($step['status'] === 'done' && $step['position'] < 6)
                <a href="{{ route('projects.step', ['project' => $project, 'position' => $step['position'] + 1]) }}">Langkah berikutnya →</a>
            @endif
        </nav>
    </article>
</div>
@endsection
