@extends('layouts.app')

@section('title', 'Jelajahi · HobbyNet')

@section('content')
@include('packs._assets')

<div class="pk">
    @include('packs._toast')

    <header class="pk-head">
        <h1 class="pk-title">{{ $q !== '' ? 'Hasil pencarian' : 'Jelajahi paket awal' }}</h1>
        <p class="pk-sub">Pilih satu, siapkan perlengkapannya, lalu mulai proyek pertamamu. Satu langkah kecil sudah cukup.</p>
    </header>

    <nav class="pk-filters" aria-label="Filter hobi">
        <a href="{{ route('explore', array_filter(['q' => $q])) }}"
           class="pk-filter {{ $hobby ? '' : 'is-active' }}">Semua</a>

        @foreach ($hobbies as $h)
            <a href="{{ route('explore', array_filter(['hobby' => $h->slug, 'q' => $q])) }}"
               class="pk-filter {{ $hobby === $h->slug ? 'is-active' : '' }}">
                <span class="pk-filter__icon" aria-hidden="true">@include('onboarding.partials.hobby-icon', ['slug' => $h->slug])</span>
                {{ $h->short_name ?? $h->name }}
            </a>
        @endforeach
    </nav>

    @if ($q !== '')
        <p class="pk-note">Hasil untuk “{{ $q }}” · {{ $packs->count() }} paket</p>
    @endif

    @if ($packs->isEmpty())
        <div class="empty">
            <p>{{ $q !== '' ? 'Belum ada paket yang cocok dengan pencarianmu.' : 'Belum ada paket untuk hobi ini. Coba hobi lain dulu.' }}</p>
            <a class="btn btn--primary" href="{{ route('explore') }}">Lihat semua paket</a>
        </div>
    @else
        <div class="pk-grid">
            @foreach ($packs as $pack)
                @include('packs._card', ['pack' => $pack])
            @endforeach
        </div>
    @endif
</div>
@endsection
