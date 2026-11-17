@extends('layouts.app')

@section('title', $project->title)

@section('content')
<link rel="stylesheet" href="{{ asset('css/guided.css') }}">
<link rel="stylesheet" href="{{ asset('css/plus-lock.css') }}">

@php
    $locked = $locked ?? false;
    $premium = $premium ?? false;
    $total = $summary['total'];
    $first = $first ?? null;
    $firstImage = $first?->instructions->first(fn ($i) => filled($i->image));
@endphp

<div class="gp">
    @include('roadmap.partials.flash')

    {{-- ===== Hero ===== --}}
    <section class="gp-hero" aria-labelledby="gp-title">
        <div class="gp-hero__media gp-tone--{{ $project->color ?? 'pink' }}">
            <a class="gp-back" href="{{ route('explore') }}" aria-label="Back to explore">@include('roadmap.partials.icon', ['name' => 'chev-left'])</a>
            @if ($project->hobby)
                <span class="gp-hero__art">@includeIf('roadmap.partials.art', ['hobby' => $project->hobby])</span>
            @endif
            @if ($project->image)
                <img src="{{ asset($project->image) }}" alt="" onerror="this.remove()">
            @endif
        </div>

        <div class="gp-hero__body">
            @if ($premium)
                <span class="gp-plus">&#9733; HobbyNet Plus</span>
            @endif
            <h1 id="gp-title">{{ $project->title }}</h1>
            <p class="gp-lead">{{ $project->description ?? $project->summary }}</p>

            <dl class="gp-meta">
                <div><dt>Kategori</dt><dd>{{ $project->category }}</dd></div>
                <div><dt>Level</dt><dd>{{ $project->level_label }}</dd></div>
                <div><dt>Waktu</dt><dd>{{ \App\Models\Project::humanDuration($summary['total_minutes']) }}</dd></div>
                <div><dt>Langkah</dt><dd>{{ $total }} {{ $total === 1 ? 'Step' : 'Steps' }}</dd></div>
            </dl>

            @if ($total > 0)
                <div class="gp-progress">
                    <div class="gp-progress__row"><span>Progres Proyek</span><strong>{{ $summary['percent'] }}%</strong></div>
                    <div class="gp-bar" role="progressbar" aria-valuenow="{{ $summary['percent'] }}" aria-valuemin="0" aria-valuemax="100" aria-label="Project progress">
                        <span style="width: {{ $summary['percent'] }}%"></span>
                    </div>
                </div>

                @if ($locked)
                    <div class="gp-lock">
                        <p><strong>Proyek ini merupakan bagian dari HobbyNet Plus.</strong> Anda dapat melihat pratinjau detail, bahan, dan langkah 1. Buka akses ke seluruh langkah, daftar periksa, serta fitur berbagi dengan berlangganan Plus.</p>
                        <div class="gp-actions">
                            <a class="gp-btn gp-btn--plus" href="{{ route('plus') }}">&#9733; Buka dengan Plus</a>
                            @if ($first)
                                <a class="gp-btn gp-btn--ghost" href="{{ route('projects.steps.show', [$project->slug, $first->slug]) }}">Lihat step 1</a>
                            @endif
                        </div>
                    </div>
                @else
                    <div class="gp-actions">
                        <form method="POST" action="{{ route('projects.start', $project->slug) }}">
                            @csrf
                            <button type="submit" class="gp-btn gp-btn--primary">
                                @if ($summary['is_complete']) Review Proyek
                                @elseif ($started && $summary['completed'] > 0) Lanjutkan Proyek
                                @else Mulai Proyek @endif
                            </button>
                        </form>
                    </div>
                @endif
            @else
                <p class="gp-note">Langkah-langkah untuk proyek ini sedang disiapkan. Silakan periksa kembali nanti.</p>
            @endif
        </div>
    </section>

    @if ($total > 0)
    <div class="gp-grid">
        {{-- ===== Tools & Materials ===== --}}
        @if ($checklist)
            <div class="gp-card">@include('partials.pack-checklist', ['checklist' => $checklist])</div>
        @endif

        <div class="gp-col">
            {{-- ===== What You'll Learn ===== --}}
            @if (count($learnings))
                <section class="gp-card" aria-labelledby="gp-learn-title">
                    <h2 id="gp-learn-title">Akan Dipelajari</h2>
                    <ul class="gp-learn">
                        @foreach ($learnings as [$title, $desc])
                            <li>
                                <span class="gp-learn__icon">@include('roadmap.partials.icon', ['name' => 'check'])</span>
                                <span><strong>{{ $title }}</strong><small>{{ $desc }}</small></span>
                            </li>
                        @endforeach
                    </ul>
                </section>
            @endif

            {{-- ===== Step 1 preview ===== --}}
            @if ($first)
                <section class="gp-card gp-preview" aria-labelledby="gp-prev-title">
                    <div class="gp-preview__media gp-tone--{{ $project->color ?? 'pink' }}">
                        @if ($firstImage)
                            <img src="{{ asset($firstImage->image) }}" alt="{{ $firstImage->image_caption ?? $first->title }}">
                        @elseif ($project->hobby)
                            <span class="gp-hero__art">@includeIf('roadmap.partials.art', ['hobby' => $project->hobby])</span>
                        @endif
                    </div>
                    <div class="gp-preview__text">
                        <p class="gp-eyebrow">Langkah 1</p>
                        <h3 id="gp-prev-title">Step 1: {{ $first->title }}</h3>
                        <p>{{ $first->summary }}</p>
                    </div>
                    @if ($locked)
                        <a class="gp-btn gp-btn--ghost gp-btn--sm" href="{{ route('projects.steps.show', [$project->slug, $first->slug]) }}">Preview</a>
                    @else
                        <form method="POST" action="{{ route('projects.start', $project->slug) }}">
                            @csrf
                            <button type="submit" class="gp-btn gp-btn--primary gp-btn--sm">{{ $started ? 'Lanjutkan' : 'Mulai Proyek' }}</button>
                        </form>
                    @endif
                </section>
            @endif
        </div>
    </div>
    @endif
</div>
@endsection
