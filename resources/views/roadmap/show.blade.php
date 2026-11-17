@extends('layouts.app')

@section('title', 'Your ' . $hobby->name . ' Journey')

@section('content')
<link rel="stylesheet" href="{{ asset('css/roadmap.css') }}">

@php
    $stepUrl = fn ($s) => $s->isShare()
        ? route('roadmap.submission.show', $hobby->slug)
        : route('roadmap.steps.show', [$hobby->slug, $s->slug]);
    $total = $summary['total'];
    $hours = round($summary['total_minutes'] / 60, 1);
    $nextBadgeStep = $summary['steps']->first(fn ($s) => $s->state !== 'completed' && $s->badge);
    $ratio = $total > 0 ? $summary['completed'] / $total : 0;
@endphp

<div class="rm-page">
<div class="rm-body">
<div class="rm-container">
    @include('roadmap.partials.flash')

    <nav class="rm-crumbs" aria-label="Breadcrumb">
        <a href="{{ url('/') }}">Home</a> &nbsp;/&nbsp; <span>My Roadmap</span>
    </nav>

    <div class="rm-head">
        <div>
            <h1 class="rm-title">Perjalanan {{ $hobby->name }}mu</h1>
            <p class="rm-lead">Kuasai dasar-dasarnya dan buat proyek {{ strtolower($project->category) }} pertama Anda!</p>
        </div>

        @if ($otherHobbies->isNotEmpty())
            <details class="rm-switch">
                <summary>@include('roadmap.partials.icon', ['name' => 'chev-left']) Ganti Hobi</summary>
                <ul class="rm-switch__menu">
                    @foreach ($otherHobbies as $other)
                        <li><a href="{{ route('roadmap.show', $other->slug) }}">{{ $other->name }}</a></li>
                    @endforeach
                </ul>
            </details>
        @endif
    </div>

    <section class="rm-card rm-progress" aria-label="Your progress">
        <div class="rm-progress__left">
            <div class="rm-progress__row">
                <span>Progres Roadmap</span>
                <strong>{{ $summary['percent'] }}% Selesai</strong>
            </div>
            <div class="rm-bar" role="progressbar" aria-valuenow="{{ $summary['percent'] }}" aria-valuemin="0" aria-valuemax="100" aria-label="Roadmap progress">
                <span class="rm-bar__fill" style="width: {{ $summary['percent'] }}%"></span>
            </div>
            <p class="rm-progress__note">{{ $summary['completed'] }} of {{ $total }} checkpoint selesai</p>
        </div>
        <span class="rm-progress__divider" aria-hidden="true"></span>
        <div class="rm-progress__right">
            <span class="rm-progress__icon">@include('roadmap.partials.icon', ['name' => 'clock'])</span>
            <div>
                <small>Waktu Penyelesaian</small>
                <strong>~{{ $hours }} jam</strong>
            </div>
        </div>
    </section>

    <ol class="rm-timeline">
        @foreach ($summary['steps'] as $s)
            @php
                $isFinal = $loop->last;
                $side = $isFinal ? 'final' : ($loop->odd ? 'right' : 'left');
            @endphp
            <li class="rm-tl rm-tl--{{ $side }} rm-tl--{{ $s->state }}">
                @unless ($isFinal)
                    <span class="rm-tl__dot" aria-hidden="true">
                        @if ($s->state === 'completed') @include('roadmap.partials.icon', ['name' => 'check'])
                        @elseif ($s->state === 'locked') @include('roadmap.partials.icon', ['name' => 'lock'])
                        @endif
                    </span>
                @endunless

                @if ($s->state === 'active')
                    <div class="rm-node rm-node--active {{ $isFinal ? 'rm-node--final' : '' }}">
                        <span class="rm-tag">Langkah Sekarang</span>
                        <h2>{{ $s->title }}</h2>
                        <p>Goal hari ini: {{ $s->summary }}</p>
                        <div class="rm-node__cta">
                            <a href="{{ $stepUrl($s) }}" class="rm-btn rm-btn--blue">Lanjutkan Perjalanan @include('roadmap.partials.icon', ['name' => 'chev-right'])</a>
                            <span>@include('roadmap.partials.icon', ['name' => 'clock']) {{ $s->estimated_minutes }} min</span>
                        </div>
                    </div>

                @elseif ($s->state === 'completed')
                    <a href="{{ $stepUrl($s) }}" class="rm-node rm-node--completed {{ $isFinal ? 'rm-node--final' : '' }}">
                        <span class="rm-thumb" aria-hidden="true">@include('roadmap.partials.icon', ['name' => $hobby->slug === 'painting' ? 'brush' : ($hobby->slug === 'photography' ? 'camera' : 'yarn')])</span>
                        <span>
                            <h2>{{ $s->title }}</h2>
                            <p class="rm-node__sub">Selesai &bull; {{ $s->completed_at?->format('M j') }}</p>
                        </span>
                    </a>

                @elseif ($isFinal)
                    <div class="rm-node rm-node--locked rm-node--final">
                        <span class="rm-final__icon" aria-hidden="true">@include('roadmap.partials.icon', ['name' => 'arrow-right'])</span>
                        <h2>{{ $s->title }}</h2>
                        <p>{{ $s->summary }}</p>
                        <span class="rm-final__lock">@include('roadmap.partials.icon', ['name' => 'lock']) Buka dengan menyelesaikan langkah {{ $total - 1 }}</span>
                    </div>

                @else
                    <div class="rm-node rm-node--locked">
                        <span class="rm-thumb" aria-hidden="true">@include('roadmap.partials.icon', ['name' => $side === 'left' ? 'bulb' : 'plus'])</span>
                        <span>
                            <h2>{{ $s->title }}</h2>
                            <p class="rm-node__sub">Checkpoint terkunci</p>
                        </span>
                    </div>
                @endif
            </li>
        @endforeach
    </ol>

    @if ($summary['is_complete'])
        <p class="rm-done-note">Kamu sudah menyelesaikan semua langkah.
            <a href="{{ route('roadmap.celebration', $hobby->slug) }}"><strong> Lihat Karyamu</strong></a>
        </p>
    @elseif (Route::has('profile'))
        <div class="rm-pills">
            <a href="{{ route('profile') }}" class="rm-pill-link">@include('roadmap.partials.icon', ['name' => 'star']) Lihat Semua Lencana</a>
        </div>
    @endif
</div>
</div>

@if ($nextBadgeStep && ! session('badges'))
    <aside class="rm-next" aria-label="Next achievement">
        <span class="rm-next__ring">
            <svg class="rm-ring" viewBox="0 0 36 36" aria-hidden="true">
                <circle cx="18" cy="18" r="16" fill="none" stroke="#EFE3DD" stroke-width="2.5"/>
                <circle cx="18" cy="18" r="16" fill="none" stroke="#879F69" stroke-width="2.5" stroke-linecap="round"
                        stroke-dasharray="{{ round($ratio * 100.5, 2) }} 100.5" transform="rotate(-90 18 18)"/>
            </svg>
            <span style="color: var(--rm-green-dark); font-size: 18px; position: relative;">@include('roadmap.partials.icon', ['name' => 'compass'])</span>
        </span>
        <span>
            <span class="rm-next__eyebrow">Pencapaian Selanjutnya</span>
            <strong>{{ $nextBadgeStep->badge->name }}</strong>
            <small>Finish &ldquo;{{ $nextBadgeStep->title }}&rdquo;</small>
        </span>
    </aside>
@endif
</div>
@endsection
