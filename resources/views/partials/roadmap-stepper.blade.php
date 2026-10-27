{{-- Variabel: $steps; opsional $link (true) + $project agar langkah yang sudah dibuka bisa diklik --}}
<ol class="stepper" aria-label="Langkah roadmap">
    @foreach ($steps as $s)
        @php($linked = ($link ?? false) && $s['status'] !== 'todo')
        <li class="stepper__item stepper__item--{{ $s['status'] }}">
            @if ($linked)
                <a class="stepper__dot" href="{{ route('projects.step', ['project' => $project, 'position' => $s['position']]) }}"
                   aria-label="Langkah {{ $s['position'] }}: {{ $s['title'] }}"
                   @if ($s['status'] === 'current') aria-current="step" @endif>{{ $s['position'] }}</a>
            @else
                <span class="stepper__dot">{{ $s['position'] }}</span>
            @endif
            <span class="stepper__label">{{ $s['label'] }}</span>
            <span class="sr-only">{{ $s['status'] === 'done' ? 'selesai' : ($s['status'] === 'current' ? 'langkah saat ini' : 'belum dimulai') }}</span>
        </li>
    @endforeach
</ol>
