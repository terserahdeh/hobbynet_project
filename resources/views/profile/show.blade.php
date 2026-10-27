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

        @if ($nextBadge)
            @php $nextPercent = (int) round(min(1, $nextBadge['value'] / max(1, $nextBadge['target'])) * 100); @endphp
            <div class="pk-next">
                <p class="pk-next__label">Lencana berikutnya</p>
                <div class="pk-next__row">
                    <span class="pk-badge__icon pk-badge__icon--{{ $nextBadge['badge']->color === 'pink' ? 'pink' : ($nextBadge['badge']->color === 'blue' ? 'blue' : 'sage') }}">@include('packs._icon', ['name' => $nextBadge['badge']->icon])</span>
                    <div>
                        <h3>{{ $nextBadge['badge']->name }}</h3>
                        <p>Selesaikan &ldquo;{{ $nextBadge['title'] }}&rdquo; ({{ $nextBadge['value'] }}/{{ $nextBadge['target'] }})</p>
                    </div>
                </div>
                <div class="bar" role="progressbar" aria-valuemin="0" aria-valuemax="100" aria-valuenow="{{ $nextPercent }}" aria-label="Kemajuan lencana">
                    <span style="width: {{ $nextPercent }}%"></span>
                </div>
            </div>
        @endif
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
            <h2 class="pk-h2">Lencana <span class="pk-muted">({{ $earnedCount }}/{{ $badges->count() }})</span></h2>
            <div class="pk-badges">
                @foreach ($badges as $badge)
                    <div class="pk-badge {{ $badge['earned'] ? '' : 'is-locked' }}">
                        <span class="pk-badge__icon pk-badge__icon--{{ $badge['tone'] }}">
                            @include('packs._icon', ['name' => $badge['earned'] ? $badge['icon'] : 'lock'])
                        </span>
                        <div>
                            <h3>{{ $badge['name'] }}</h3>
                            <p>{{ $badge['desc'] }}</p>
                            @if ($badge['earned'] && $badge['earned_at'])
                                <p class="pk-badge__date">Diraih {{ \Illuminate\Support\Carbon::parse($badge['earned_at'])->locale('id')->translatedFormat('j F Y') }}</p>
                            @endif
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
            @if ($completed->isNotEmpty())
                <div class="pk-done">
                    @foreach ($completed as $sub)
                        <article class="pk-done__card">
                            <div class="pk-done__media">
                                @if ($sub->photos->isNotEmpty())
                                    <img src="{{ $sub->photos->first()->url }}" alt="{{ $sub->title ?: $sub->project->title }}" loading="lazy">
                                @endif
                            </div>
                            <div class="pk-done__body">
                                <h3>{{ $sub->title ?: $sub->project->title }}</h3>
                                <p>{{ $sub->project->title }}</p>
                                @if ($sub->submitted_at)
                                    <p class="pk-muted">Selesai {{ $sub->submitted_at->locale('id')->translatedFormat('j F Y') }}</p>
                                @endif
                            </div>
                        </article>
                    @endforeach
                </div>
            @else
                <div class="empty">
                    <p>Belum ada proyek yang selesai. Saat kamu menyelesaikan proyek pertamamu, hasilnya akan muncul di sini.</p>
                    <a class="btn btn--primary" href="{{ route('roadmap.index') }}">Mulai dari peta belajarmu</a>
                </div>
            @endif
        </section>
    </div>
</div>
@endsection
