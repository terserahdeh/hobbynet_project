@extends('layouts.app')

@section('title', 'Profil · HobbyNet')

@section('content')
@include('packs._assets')

<div class="pk pk--profile">
    {{-- ===== Samping ===== --}}
    <aside class="pk-profile__side">
        <nav class="pk-nav" aria-label="Menu profil">
            <a href="{{ route('profile') }}" class="is-active" aria-current="page">
                @include('packs._icon', ['name' => 'user']) Profilku
            </a>
            <a href="{{ route('bookmarks') }}">
                @include('packs._icon', ['name' => 'bookmark']) Tersimpan
                @if ($savedCount > 0)
                    <span class="pk-count">{{ $savedCount }}</span>
                @endif
            </a>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit">Keluar</button>
            </form>
        </nav>

        <div class="pk-next">
            <p class="pk-next__label">Lencana berikutnya</p>
            <div class="pk-next__row">
                <span class="pk-badge__icon pk-badge__icon--blue">@include('packs._icon', ['name' => 'compass'])</span>
                <div>
                    <h3>Penjelajah Penasaran</h3>
                    <p>Coba 2 hobi (0/2)</p>
                </div>
            </div>
            <div class="bar" role="progressbar" aria-valuemin="0" aria-valuemax="100" aria-valuenow="0" aria-label="Kemajuan lencana">
                <span style="width: 0%"></span>
            </div>
        </div>
    </aside>

    {{-- ===== Isi ===== --}}
    <div>
        <section class="pk-profile-head">
            <div class="pk-avatar" aria-hidden="true">{{ $initial }}</div>
            <div>
                <h1 class="pk-title">{{ $user->name }}</h1>

                @if ($hobbies->isNotEmpty())
                    <ul class="chips" aria-label="Minat">
                        @foreach ($hobbies as $hobby)
                            <li class="chip chip--{{ $hobby->color }}">
                                <span class="chip__icon" aria-hidden="true">@include('onboarding.partials.hobby-icon', ['slug' => $hobby->slug])</span>
                                {{ $hobby->short_name }}
                            </li>
                        @endforeach
                    </ul>
                @else
                    <p class="pk-muted">
                        Belum memilih hobi.
                        <a class="pk-link" href="{{ route('onboarding.hobbies') }}">Pilih hobi</a>
                    </p>
                @endif

                @if ($since)
                    <p class="pk-muted pk-since">Bergabung sejak {{ $since }}</p>
                @endif
            </div>
        </section>

        <section class="pk-block">
            <h2 class="pk-h2">Lencana</h2>
            <div class="pk-badges">
                @foreach ($badges as $badge)
                    <div class="pk-badge {{ $badge['earned'] ? '' : 'is-locked' }}">
                        <span class="pk-badge__icon pk-badge__icon--{{ $badge['tone'] }}">
                            @include('packs._icon', ['name' => $badge['earned'] ? $badge['icon'] : 'lock'])
                        </span>
                        <div>
                            <h3>{{ $badge['name'] }}</h3>
                            <p>{{ $badge['desc'] }}</p>
                        </div>
                    </div>
                @endforeach

                <div class="pk-badge is-locked">
                    <span class="pk-badge__icon">@include('packs._icon', ['name' => 'lock'])</span>
                    <div>
                        <h3>Masih ada lagi…</h3>
                        <p>Terus berkarya untuk membuka lencana baru.</p>
                    </div>
                </div>
            </div>
        </section>

        <section class="pk-block">
            <h2 class="pk-h2">Proyek Selesai</h2>
            <div class="empty">
                <p>Belum ada proyek yang selesai. Saat kamu menyelesaikan proyek pertamamu, hasilnya akan muncul di sini.</p>
                <a class="btn btn--primary" href="{{ route('explore') }}">Mulai dari Jelajahi</a>
            </div>
        </section>
    </div>
</div>
@endsection
