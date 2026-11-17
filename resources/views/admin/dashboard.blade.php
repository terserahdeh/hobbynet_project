@extends('admin.layouts.admin')

@section('title', 'Overview')

@section('content')
<div class="admin-head">
    <div>
        <h1>Welcome back, {{ \Illuminate\Support\Str::before(auth()->user()->name, ' ') }}</h1>
        <p>Build hobbies and roadmaps here. Publish a roadmap and learners see it straight away.</p>
    </div>
    <div class="admin-head__actions">
        <a href="{{ route('admin.projects.create') }}" class="admin-btn admin-btn--primary">@include('roadmap.partials.icon', ['name' => 'plus']) New project</a>
        <a href="{{ route('admin.hobbies.create') }}" class="admin-btn admin-btn--ghost">New hobby</a>
    </div>
</div>

<div class="admin-stats">
    <div class="admin-stat"><strong>{{ $stats['hobbies'] }}</strong><span>Hobbies</span></div>
    <div class="admin-stat"><strong>{{ $stats['roadmaps'] }}</strong><span>Live roadmaps</span></div>
    <div class="admin-stat"><strong>{{ $stats['steps'] }}</strong><span>Steps</span></div>
    <div class="admin-stat"><strong>{{ $stats['badges'] }}</strong><span>Badges</span></div>
    <div class="admin-stat"><strong>{{ $stats['learners'] }}</strong><span>Learners started</span></div>
    <div class="admin-stat"><strong>{{ $stats['submissions'] }}</strong><span>Projects shared</span></div>
</div>

<section class="admin-card">
    <div class="admin-card__head">
        <div>
            <h2>Roadmaps</h2>
            <p class="admin-card__sub">Every hobby's starter project and the guided steps behind it.</p>
        </div>
        <a href="{{ route('admin.projects.index') }}" class="admin-btn admin-btn--ghost admin-btn--sm">All projects</a>
    </div>

    @if ($roadmaps->isEmpty())
        <div class="admin-empty">
            <strong>No roadmaps yet</strong>
            Create a hobby, then a starter project for it, and add its steps.
        </div>
    @else
        <div class="admin-scroll">
            <table class="admin-table">
                <thead>
                    <tr><th>Roadmap</th><th>Hobby</th><th>Steps</th><th>Status</th><th></th></tr>
                </thead>
                <tbody>
                @foreach ($roadmaps as $project)
                    <tr>
                        <td><strong>{{ $project->title }}</strong></td>
                        <td>{{ $hobbies[$project->hobby_id]->name ?? '—' }}</td>
                        <td>{{ $stepCounts[$project->id] ?? 0 }}</td>
                        <td>
                            @if ($project->roadmap_published)
                                <span class="admin-pill admin-pill--green">Live</span>
                            @else
                                <span class="admin-pill admin-pill--amber">Draft</span>
                            @endif
                        </td>
                        <td class="is-actions">
                            <a href="{{ route('admin.projects.steps.index', $project) }}" class="admin-btn admin-btn--ghost admin-btn--sm">Manage steps</a>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    @endif
</section>
@endsection
