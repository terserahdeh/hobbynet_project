@extends('layouts.app')

@section('title', 'You did it!')

@section('content')
<link rel="stylesheet" href="{{ asset('css/roadmap.css') }}">

<div class="rm-page">
    <header class="rm-topbar">
        <div class="rm-topbar__left">
            <a href="{{ route('projects.show', $project->slug) }}" class="rm-topbar__close" aria-label="Back to your journey">@include('roadmap.partials.icon', ['name' => 'close'])</a>
            <div class="rm-topbar__title">
                <strong>{{ $project->title }}</strong>
                <span>Apresiasi</span>
            </div>
        </div>
    </header>

    <div class="rm-body">
    <div class="rm-container rm-container--celebrate rm-center">
        @include('roadmap.partials.flash')

        <div class="rm-burst" aria-hidden="true">
            <span class="rm-burst__core">@include('roadmap.partials.icon', ['name' => 'heart'])</span>
            <span class="rm-burst__check">@include('roadmap.partials.icon', ['name' => 'check'])</span>
        </div>
        <h1 class="rm-hero">Kamu berhasil!</h1>
        <p class="rm-sub" style="margin-top: 5px;">Projek pertama mu selesai.</p>

        <div class="rm-grid2">
            <article class="rm-card rm-creation">
                <div class="rm-creation__media">
                    @if ($submission->photos->isNotEmpty())
                        <img src="{{ $submission->photos->first()->url }}" alt="{{ $submission->title }}">
                    @else
                        <div class="rm-creation__noimg">@include('roadmap.partials.icon', ['name' => 'image'])</div>
                    @endif
                    <span class="rm-creation__tag">Dikuasai</span>
                </div>
                <div class="rm-creation__body">
                    <h2>{{ $submission->title }}</h2>
                    <p class="rm-creation__series">{{ $project->title }}</p>
                    <div class="rm-creation__foot">
                        <span>Roadmap Selesai</span>
                        <b>{{ $summary['completed'] }} of {{ $summary['total'] }} checkpoint</b>
                    </div>
                </div>
            </article>

            <div class="rm-side">
                @if ($badge)
                    <article class="rm-card rm-badge">
                        <div class="rm-badge__wrap">
                            <div class="rm-badge__ring">@include('roadmap.partials.icon', ['name' => 'compass'])</div>
                            <span class="rm-badge__label">Lencana</span>
                        </div>
                        <h2>{{ $badge->name }}</h2>
                        <p>{{ $badge->description }}</p>
                    </article>
                @endif

                @if (Route::has('profile'))
                    <a href="{{ route('profile') }}" class="rm-btn rm-btn--soft-green">Lihat Pencapaianku @include('roadmap.partials.icon', ['name' => 'chev-right'])</a>
                @endif

                @if ($nextProject && Route::has('projects.show'))
                    <a href="{{ route('projects.show', $nextProject->slug) }}" class="rm-btn rm-btn--soft-pink">Eksplor Projek Selanjutnya @include('roadmap.partials.icon', ['name' => 'bulb'])</a>
                @endif
            </div>
        </div>

        <p class="rm-after">
            @if ($submission->is_showcase)
                <span class="is-green">@include('roadmap.partials.icon', ['name' => 'image']) Dibagikan ke Galeri</span>
                <i class="rm-after__dot" aria-hidden="true"></i>
            @endif
            <a href="{{ url('/') }}">@include('roadmap.partials.icon', ['name' => 'chev-right']) Kembali ke Dashboard</a>
        </p>
    </div>
    </div>
</div>
@endsection
