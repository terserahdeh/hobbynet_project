@extends('layouts.app')

@section('title', 'You did it!')

@section('content')
<link rel="stylesheet" href="{{ asset('css/roadmap.css') }}">

<div class="rm-page">
    <header class="rm-topbar">
        <div class="rm-topbar__left">
            <a href="{{ route('roadmap.show', $hobby->slug) }}" class="rm-topbar__close" aria-label="Back to your journey">@include('roadmap.partials.icon', ['name' => 'close'])</a>
            <div class="rm-topbar__title">
                <strong>{{ $project->title }}</strong>
                <span>Celebration</span>
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
        <h1 class="rm-hero">You did it!</h1>
        <p class="rm-sub" style="margin-top: 12px;">Your first project is complete.</p>

        <div class="rm-grid2">
            <article class="rm-card rm-creation">
                <div class="rm-creation__media">
                    @if ($submission->photos->isNotEmpty())
                        <img src="{{ $submission->photos->first()->url }}" alt="{{ $submission->title }}">
                    @else
                        <div class="rm-creation__noimg">@include('roadmap.partials.icon', ['name' => 'image'])</div>
                    @endif
                    <span class="rm-creation__tag">Mastered</span>
                </div>
                <div class="rm-creation__body">
                    <h2>{{ $submission->title }}</h2>
                    <p class="rm-creation__series">{{ $project->title }}</p>
                    <div class="rm-creation__foot">
                        <span>Roadmap Completed</span>
                        <b>{{ $summary['completed'] }} of {{ $summary['total'] }} checkpoints</b>
                    </div>
                </div>
            </article>

            <div class="rm-side">
                @if ($badge)
                    <article class="rm-card rm-badge">
                        <div class="rm-badge__wrap">
                            <div class="rm-badge__ring">@include('roadmap.partials.icon', ['name' => in_array($badge->icon, ['compass','star','check','heart','yarn']) ? $badge->icon : ($badge->icon === 'sparkle' ? 'heart' : 'star')])</div>
                            <span class="rm-badge__label">Badge</span>
                        </div>
                        <h2>{{ $badge->name }}</h2>
                        <p>{{ $badge->description }}</p>
                    </article>
                @endif

                <a href="{{ route('profile') }}" class="rm-btn rm-btn--soft-green">View My Achievement @include('roadmap.partials.icon', ['name' => 'chev-right'])</a>

                @if ($nextProject)
                    <a href="{{ route('projects.show', $nextProject->slug) }}" class="rm-btn rm-btn--soft-pink">Explore My Next Project @include('roadmap.partials.icon', ['name' => 'bulb'])</a>
                @endif
            </div>
        </div>

        <p class="rm-after">
            @if ($submission->is_showcase)
                <span class="is-green">@include('roadmap.partials.icon', ['name' => 'image']) Shared to gallery</span>
                <i class="rm-after__dot" aria-hidden="true"></i>
            @endif
            <a href="{{ route('home') }}">@include('roadmap.partials.icon', ['name' => 'chev-right']) Back to dashboard</a>
        </p>
    </div>
    </div>
</div>
@endsection
