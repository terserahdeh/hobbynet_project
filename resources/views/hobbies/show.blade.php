@extends('layouts.app')

@section('title', $hobby->short_name . ' · Hobby Enabler')

@section('content')
<div class="wide">
    <a class="back" href="{{ route('explore') }}">← Jelajahi</a>

    @if (session('status'))
        <p class="flash" role="status">{{ session('status') }}</p>
    @endif

    <header class="hobbyhead">
        <span class="hobbyhead__icon thumb--{{ $hobby->color }}" aria-hidden="true">
            @include('partials.hobby-icon', ['slug' => $hobby->slug])
        </span>
        <div class="hobbyhead__text">
            <h1>{{ $hobby->name }}</h1>
            <p>{{ $hobby->tagline }}</p>
        </div>
        @if ($chosen)
            <span class="pill">Ada di minatmu</span>
        @else
            <form method="POST" action="{{ route('hobbies.add', $hobby) }}">
                @csrf
                <button type="submit" class="btn btn--ghost">Tambah ke minatku</button>
            </form>
        @endif
    </header>

    @forelse ($groups as $group)
        <section class="block" aria-labelledby="h-{{ $group['level'] }}">
            <h2 class="section-title" id="h-{{ $group['level'] }}">{{ $group['title'] }}</h2>
            <ul class="tiles tiles--grid">
                @foreach ($group['projects'] as $project)
                    <li>
                        @include('partials.project-tile', [
                            'project' => $project,
                            'badge'   => in_array($project->id, $inList, true) ? 'Di daftarmu' : null,
                        ])
                    </li>
                @endforeach
            </ul>
        </section>
    @empty
        <div class="empty"><p>Proyek untuk hobi ini sedang disiapkan.</p></div>
    @endforelse
</div>
@endsection
