@extends('admin.layouts.admin')

@section('title', 'Projects & roadmaps')

@section('content')
<div class="ad-head">
    <div>
        <h1>Projects &amp; roadmaps</h1>
        <p>A hobby's <strong>starter project</strong> becomes its guided roadmap. Other projects are browsable ideas.</p>
    </div>
    <a href="{{ route('admin.projects.create') }}" class="ad-btn ad-btn--primary">@include('roadmap.partials.icon', ['name' => 'plus']) New project</a>
</div>

<section class="ad-card">
    @if ($projects->isEmpty())
        <div class="ad-empty"><strong>No projects yet</strong>Create one and mark it as a hobby's starter project to give it a roadmap.</div>
    @else
        <div class="ad-scroll">
            <table class="ad-table">
                <thead>
                    <tr><th>Project</th><th>Hobby</th><th>Level</th><th>Roadmap</th><th>Steps</th><th></th></tr>
                </thead>
                <tbody>
                @foreach ($projects as $project)
                    <tr>
                        <td>
                            <strong>{{ $project->title }}</strong>
                            <small>{{ $project->category }}@if ($project->is_popular) &middot; Popular @endif</small>
                        </td>
                        <td>{{ $hobbies[$project->hobby_id]->name ?? '—' }}</td>
                        <td>{{ ucfirst($project->level) }}</td>
                        <td>
                            @if (! $project->is_starter)
                                <span class="ad-pill">Not a starter</span>
                            @elseif ($project->roadmap_published)
                                <span class="ad-pill ad-pill--green">Live</span>
                            @else
                                <span class="ad-pill ad-pill--amber">Draft</span>
                            @endif
                        </td>
                        <td>{{ $project->is_starter ? ($stepCounts[$project->id] ?? 0) : '—' }}</td>
                        <td class="is-actions">
                            @if ($project->is_starter)
                                <a href="{{ route('admin.projects.steps.index', $project) }}" class="ad-btn ad-btn--ghost ad-btn--sm">Steps</a>
                            @endif
                            <a href="{{ route('admin.projects.edit', $project) }}" class="ad-btn ad-btn--ghost ad-btn--sm">Edit</a>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    @endif
</section>
@endsection
