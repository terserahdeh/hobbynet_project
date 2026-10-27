@extends('layouts.app')

@section('title', 'Jelajahi · Hobby Enabler')

@section('content')
<div class="wide">
    <h1 class="dash__title">Jelajahi hobi</h1>
    <p class="section-sub">Penasaran dengan hal baru? Buka satu hobi dan lihat proyeknya. Hobi baru hanya bisa ditambahkan ke minatmu, tidak menggantikan yang lama.</p>

    <ul class="hobbygrid">
        @foreach ($hobbies as $hobby)
            <li>
                <a class="hobbycard" href="{{ route('hobbies.show', $hobby) }}">
                    <span class="hobbycard__icon thumb--{{ $hobby->color }}" aria-hidden="true">
                        @include('partials.hobby-icon', ['slug' => $hobby->slug])
                    </span>
                    <strong>{{ $hobby->name }}</strong>
                    <span class="hobbycard__tag">{{ $hobby->tagline }}</span>
                    <span class="hobbycard__meta">
                        {{ $hobby->projects_count }} proyek
                        @if (in_array($hobby->id, $chosen, true)) <span class="pill">Ada di minatmu</span> @endif
                    </span>
                </a>
            </li>
        @endforeach
    </ul>

    @if ($general->isNotEmpty())
        <section class="block" aria-labelledby="h-umum">
            <h2 class="section-title" id="h-umum">Tutorial dan proyek lainnya</h2>
            <ul class="tiles tiles--grid">
                @foreach ($general as $project)
                    <li>@include('partials.project-tile', ['project' => $project])</li>
                @endforeach
            </ul>
        </section>
    @endif
</div>
@endsection
