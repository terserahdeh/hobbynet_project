@extends('admin.layouts.admin')

@section('title', 'Steps · ' . $project->title)

@section('content')
@php
    $typeLabels = \App\Http\Requests\Admin\RoadmapStepRequest::TYPES;
    $ready = $problems === [];
@endphp

<nav class="ad-crumbs">
    <a href="{{ route('admin.projects.index') }}">Projects &amp; roadmaps</a> /
    <a href="{{ route('admin.projects.edit', $project) }}">{{ $project->title }}</a> / Steps
</nav>

<div class="ad-head">
    <div>
        <h1>Roadmap steps</h1>
        <p>{{ $project->title }}@if ($hobby) &middot; {{ $hobby->name }}@endif. Learners go through these in order, one at a time.</p>
    </div>
    <div class="ad-head__actions">
        <a href="{{ route('admin.projects.edit', $project) }}" class="ad-btn ad-btn--ghost">Project settings</a>
        <a href="{{ route('admin.projects.steps.create', $project) }}" class="ad-btn ad-btn--primary">@include('roadmap.partials.icon', ['name' => 'plus']) Add step</a>
    </div>
</div>

{{-- Ready to publish? --}}
<section class="ad-card ad-card__pad" style="margin-bottom: 22px;">
    <div class="ad-ready {{ $project->roadmap_published ? 'ad-ready--ok' : ($ready ? 'ad-ready--ok' : 'ad-ready--todo') }}">
        <span class="ad-ready__icon">@include('roadmap.partials.icon', ['name' => $ready ? 'check' : 'bulb'])</span>
        <div style="flex: 1;">
            @if ($project->roadmap_published)
                <strong>Live for learners</strong>
                <span class="ad-help">{{ $learners['started'] }} started &middot; {{ $learners['finished'] }} finished. Edits keep everyone's progress.</span>
            @elseif ($ready)
                <strong>Ready to publish</strong>
                <span class="ad-help">This roadmap is complete. Publish it and learners will see it.</span>
            @else
                <strong>Not ready to publish yet</strong>
                <ul>@foreach ($problems as $problem) <li>{{ $problem }}</li> @endforeach</ul>
            @endif
        </div>

        @if ($project->roadmap_published)
            <form method="POST" action="{{ route('admin.projects.publish', $project) }}" class="ad-inline">
                @csrf <input type="hidden" name="publish" value="0">
                <button type="submit" class="ad-btn ad-btn--ghost ad-btn--sm">Hide from learners</button>
            </form>
        @else
            <form method="POST" action="{{ route('admin.projects.publish', $project) }}" class="ad-inline">
                @csrf <input type="hidden" name="publish" value="1">
                <button type="submit" class="ad-btn ad-btn--primary ad-btn--sm" @disabled(! $ready)>Publish roadmap</button>
            </form>
        @endif
    </div>
</section>

<section class="ad-card">
    @if ($steps->isEmpty())
        <div class="ad-empty">
            <strong>No steps yet</strong>
            Start with “Meet Your Materials”, then add lessons, and finish with a share step.
            <div style="margin-top: 16px;">
                <a href="{{ route('admin.projects.steps.create', $project) }}" class="ad-btn ad-btn--primary">Add the first step</a>
            </div>
        </div>
    @else
        <div class="ad-scroll">
            <table class="ad-table">
                <thead>
                    <tr><th>#</th><th>Step</th><th>Type</th><th>Time</th><th>Content</th><th>Badge</th><th></th></tr>
                </thead>
                <tbody>
                @foreach ($steps as $step)
                    @php
                        $next = $steps[$loop->index + 1] ?? null;
                        $canUp = ! $step->isShare() && ! $loop->first;
                        $canDown = ! $step->isShare() && $next && ! $next->isShare();
                    @endphp
                    <tr>
                        <td class="is-num">{{ $step->position }}</td>
                        <td>
                            <strong>{{ $step->title }}</strong>
                            <small>{{ $step->summary }}</small>
                        </td>
                        <td><span class="ad-pill {{ $step->isShare() ? 'ad-pill--pink' : '' }}">{{ \Illuminate\Support\Str::before($typeLabels[$step->type] ?? ucfirst($step->type), ' (') }}</span></td>
                        <td style="white-space: nowrap;">{{ $step->estimated_minutes }} min</td>
                        <td>
                            @if ($step->isShare())
                                <small>Upload form</small>
                            @else
                                {{ $step->instructions_count }} {{ \Illuminate\Support\Str::plural('instruction', $step->instructions_count) }}
                                <small>{{ $step->checklist_items_count }} checklist &middot; {{ $step->image ? 'own picture' : 'default picture' }}</small>
                            @endif
                        </td>
                        <td>{{ $step->badge?->name ?? '—' }}</td>
                        <td class="is-actions">
                            <form method="POST" action="{{ route('admin.steps.move', [$step, 'up']) }}" class="ad-inline">
                                @csrf <button type="submit" class="ad-icon-btn" aria-label="Move up" @disabled(! $canUp)>&uarr;</button>
                            </form>
                            <form method="POST" action="{{ route('admin.steps.move', [$step, 'down']) }}" class="ad-inline">
                                @csrf <button type="submit" class="ad-icon-btn" aria-label="Move down" @disabled(! $canDown)>&darr;</button>
                            </form>
                            <a href="{{ route('admin.steps.edit', $step) }}" class="ad-btn ad-btn--ghost ad-btn--sm">Edit</a>
                            <form method="POST" action="{{ route('admin.steps.destroy', $step) }}" class="ad-inline"
                                  onsubmit="return confirm('Delete {{ addslashes($step->title) }}? Learners who finished it will lose that progress.')">
                                @csrf @method('DELETE')
                                <button type="submit" class="ad-icon-btn ad-icon-btn--danger" aria-label="Delete step">&times;</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    @endif
</section>

<p class="ad-help" style="margin-top: 16px;">The share step is always last. It is the “Look What You Made!” page where learners upload a photo of their project.</p>
@endsection
