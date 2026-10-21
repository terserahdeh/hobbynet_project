{{-- Gambar proyek. Jika file gambar belum ada, tampil gradasi pastel + ikon. --}}
<span class="thumb thumb--{{ $project->color }}">
    <span class="thumb__icon" aria-hidden="true">
        @include('onboarding.partials.hobby-icon', ['slug' => $project->icon])
    </span>
    @if ($project->image)
        <img src="{{ asset($project->image) }}" alt="" loading="lazy" onerror="this.remove()">
    @endif
</span>
