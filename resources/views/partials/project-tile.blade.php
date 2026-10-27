{{-- Kartu proyek ringkas. Variabel: $project, $badge (opsional) --}}
<a class="tile" href="{{ route('projects.show', $project) }}">
    <span class="tile__media">
        @include('partials.project-thumb', ['project' => $project])
        @isset($badge) <span class="tile__badge">{{ $badge }}</span> @endisset
    </span>
    <span class="tile__cat">{{ $project->category }}</span>
    <strong class="tile__title">{{ $project->title }}</strong>
    <span class="tile__meta">
        <span>{{ $project->learners_label }}</span>
        <span>{{ $project->level_label }}</span>
    </span>
</a>
