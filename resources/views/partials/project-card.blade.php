{{-- Kartu proyek. Variabel: $project, $status (opsional), $label (opsional) --}}
<article class="hcard">
    <a class="hcard__media" href="{{ route('projects.show', $project) }}" tabindex="-1" aria-hidden="true">
        @include('partials.project-thumb', ['project' => $project])
        @if ($project->is_starter)
            <span class="hcard__flag">Cocok untuk mulai</span>
        @endif
    </a>
    <h3 class="hcard__title">
        <a href="{{ route('projects.show', $project) }}">{{ $project->title }}</a>
    </h3>
    <p class="hcard__meta">{{ $project->category }} • {{ $project->level_label }} • {{ $project->duration_label }}</p>
    <p class="hcard__desc">{{ $project->summary }}</p>
    <div class="hcard__foot">
        <span class="status status--{{ $status ?? 'none' }}">{{ $label ?? 'Belum dimulai' }}</span>
        <a class="link-quiet" href="{{ route('projects.show', $project) }}">Lihat proyek</a>
    </div>
</article>
