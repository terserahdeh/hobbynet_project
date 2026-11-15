@extends('layouts.app')

@section('title', $project->title)

@section('content')
<link rel="stylesheet" href="{{ asset('css/guided.css') }}">

@php
    $total = $summary['total'];
    $readyCount = $materials->filter(fn ($m) => in_array($m->name, $ready, true))->count();
    $missing = $materials->reject(fn ($m) => in_array($m->name, $ready, true));
    $readyPercent = $materials->isEmpty() ? 0 : (int) round($readyCount / $materials->count() * 100);
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
            <h1 id="gp-title">{{ $project->title }}</h1>
            <p class="gp-lead">{{ $project->description ?? $project->summary }}</p>

            <dl class="gp-meta">
                <div><dt>Category</dt><dd>{{ $project->category }}</dd></div>
                <div><dt>Level</dt><dd>{{ $project->level_label }}</dd></div>
                <div><dt>Time</dt><dd>{{ \App\Models\Project::humanDuration($summary['total_minutes']) }}</dd></div>
                <div><dt>Steps</dt><dd>{{ $total }} {{ $total === 1 ? 'Step' : 'Steps' }}</dd></div>
            </dl>

            @if ($total > 0)
                <div class="gp-progress">
                    <div class="gp-progress__row"><span>Project Progress</span><strong>{{ $summary['percent'] }}%</strong></div>
                    <div class="gp-bar" role="progressbar" aria-valuenow="{{ $summary['percent'] }}" aria-valuemin="0" aria-valuemax="100" aria-label="Project progress">
                        <span style="width: {{ $summary['percent'] }}%"></span>
                    </div>
                </div>

                @if ($roadmapChoice ?? false)
                    <p class="gp-note">
                        This is a {{ strtolower($project->level_label) }} roadmap.
                        @if ($recommended) Your recommended starter is <strong>{{ $recommended->title }}</strong> ({{ $recommended->level_label }}). @endif
                        You can switch back any time from the roadmap page, and your progress on each one is kept.
                    </p>
                    <div class="gp-actions">
                        <form method="POST" action="{{ route('projects.roadmap.choose', $project->slug) }}">
                            @csrf
                            <button type="submit" class="gp-btn gp-btn--primary">Use as my {{ $project->hobby->short_name ?? $project->hobby->name }} roadmap</button>
                        </form>
                    </div>
                @else
                    <div class="gp-actions">
                        <form method="POST" action="{{ route('projects.start', $project->slug) }}">
                            @csrf
                            <button type="submit" class="gp-btn gp-btn--primary">
                                @if ($summary['is_complete']) Review Project
                                @elseif ($started && $summary['completed'] > 0) Continue Project
                                @else Start Project @endif
                            </button>
                        </form>
                    </div>
                @endif
            @else
                <p class="gp-note">The guided steps for this project are being prepared. Check back soon.</p>
            @endif
        </div>
    </section>

    @if (\App\Support\YouTube::id($project->video_url))
        <section class="gp-card" aria-labelledby="gp-video-title">
            <h2 id="gp-video-title">Tutorial Video</h2>
            @include('partials.youtube', ['url' => $project->video_url, 'title' => $project->title . ' tutorial video'])
        </section>
    @endif

    @if ($total > 0)
    <div class="gp-grid">
        {{-- ===== Tools & Materials ===== --}}
        <section class="gp-card gp-materials" aria-labelledby="gp-mat-title">
            <div class="gp-card__head">
                <h2 id="gp-mat-title">Tools &amp; Materials</h2>
                <span class="gp-count">{{ $readyCount }} of {{ $materials->count() }} ready</span>
            </div>
            <div class="gp-bar gp-bar--thin" aria-hidden="true"><span style="width: {{ $readyPercent }}%"></span></div>

            <ul class="gp-checks">
                @foreach ($materials as $m)
                    @php $isReady = in_array($m->name, $ready, true); @endphp
                    <li>
                        <form method="POST" action="{{ route('projects.materials.toggle', $project->slug) }}">
                            @csrf
                            <input type="hidden" name="name" value="{{ $m->name }}">
                            <label class="gp-check {{ $isReady ? 'is-ready' : '' }}">
                                <input type="checkbox" @checked($isReady) onchange="this.form.submit()">
                                <span>{{ $m->name }}@if ($m->spec) <small>({{ $m->spec }})</small>@endif @if ($m->is_optional) <small>optional</small>@endif</span>
                            </label>
                            <noscript><button type="submit" class="gp-link">Save</button></noscript>
                        </form>
                    </li>
                @endforeach
            </ul>

            @if ($missing->isNotEmpty())
                <details class="gp-shop">
                    <summary class="gp-btn gp-btn--dark">Shop Missing Items</summary>
                    <ul>
                        @foreach ($missing as $m)
                            <li>
                                <span>{{ $m->name }}</span>
                                <a href="{{ str_replace('%s', rawurlencode($m->name), $shopUrl) }}" target="_blank" rel="noopener noreferrer">Search</a>
                            </li>
                        @endforeach
                    </ul>
                </details>
            @else
                <p class="gp-note">You have everything you need. Ready when you are.</p>
            @endif
        </section>

        <div class="gp-col">
            {{-- ===== What You'll Learn ===== --}}
            @if (count($learnings))
                <section class="gp-card" aria-labelledby="gp-learn-title">
                    <h2 id="gp-learn-title">What You'll Learn</h2>
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
                        <p class="gp-eyebrow">Step 1 preview</p>
                        <h3 id="gp-prev-title">Step 1: {{ $first->title }}</h3>
                        <p>{{ $first->summary }}</p>
                    </div>
                    @if ($roadmapChoice ?? false)
                        <form method="POST" action="{{ route('projects.roadmap.choose', $project->slug) }}">
                            @csrf
                            <button type="submit" class="gp-btn gp-btn--primary gp-btn--sm">Use as roadmap</button>
                        </form>
                    @else
                        <form method="POST" action="{{ route('projects.start', $project->slug) }}">
                            @csrf
                            <button type="submit" class="gp-btn gp-btn--primary gp-btn--sm">{{ $started ? 'Continue' : 'Start Project' }}</button>
                        </form>
                    @endif
                </section>
            @endif
        </div>
    </div>
    @endif
</div>
@endsection
