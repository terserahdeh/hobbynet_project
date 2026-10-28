@extends('layouts.app')

@section('title', 'Profil · HobbyNet')

@section('content')
@include('packs._assets')
@push('head')<style>.hn-profile-layout{min-height:55vh}</style>@endpush

<div class="pk pk--profile hn-profile-layout">
    @if(session('status'))<div role="status" class="pk-note">{{ session('status') }}</div>@endif
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
    <div class="hn-profile-main">
        <section class="pk-profile-head">
            @if($user->avatar_path)<img class="pk-avatar" src="{{ asset('storage/'.$user->avatar_path) }}" alt="Foto profil {{ $user->name }}" style="width:86px;height:86px;object-fit:cover;border-radius:50%">@else<div class="pk-avatar" aria-hidden="true">{{ $initial }}</div>@endif
            <div>
                <h1 class="pk-title">{{ $user->name }}</h1>
                @if($user->nickname)<p class="pk-muted">{{ '@' . $user->nickname }}</p>@endif
                <a class="btn btn--ghost" href="{{ route('profile.edit') }}" style="margin-top:12px">✎ Edit profil</a>

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

       
{{-- ================================
     HOBBYNET - COLORFUL BADGES
     ================================ --}}

<section class="pk-block hn-achievements">

    <div class="hn-section-heading">
        <div>
            <p class="hn-eyebrow">PENCAPAIANMU</p>

            <h2 class="pk-h2">
                Lencana
                <span class="hn-total">
                    {{ $earnedCount }}/{{ $badges->count() }}
                </span>
            </h2>

            <p class="hn-subtitle">
                Setiap langkah kecil layak dirayakan.
                Kumpulkan lencana dan tunjukkan pencapaianmu!
            </p>
        </div>
    </div>

    @php
        $badgeThemes = [
            ['start' => '#FFE0A8', 'end' => '#FFF5DF', 'accent' => '#A96A22'],
            ['start' => '#C8DAFF', 'end' => '#F0F4FF', 'accent' => '#5069B6'],
            ['start' => '#FFC5DE', 'end' => '#FFF0F7', 'accent' => '#B44D80'],
            ['start' => '#BCEBCF', 'end' => '#EDFFF4', 'accent' => '#38835D'],
            ['start' => '#DEC8FF', 'end' => '#F8F0FF', 'accent' => '#8056B5'],
            ['start' => '#BFEAFF', 'end' => '#EFFAFF', 'accent' => '#3484A5'],
        ];
    @endphp

    <div class="pk-badges hn-badges-v8">

        @foreach ($badges as $badge)

            @php
                $theme = $badgeThemes[
                    $loop->index % count($badgeThemes)
                ];

                $gradient = "linear-gradient(135deg, {$theme['start']}, {$theme['end']})";
            @endphp

            <div
                class="pk-badge hn-badge-v8 {{ $badge['earned'] ? 'is-earned' : 'is-locked' }}"
                style="
                    background: {{ $gradient }} !important;
                    border: 1px solid {{ $theme['accent'] }} !important;
                    --accent: {{ $theme['accent'] }};
                "
            >

                <div class="hn-badge-v8__symbol">
                    @include('packs._icon', [
                        'name' => $badge['earned']
                            ? $badge['icon']
                            : 'lock'
                    ])
                </div>

                <div class="hn-badge-v8__content">

                    <span class="hn-badge-v8__status">
                        {{ $badge['earned'] ? '✦ DIPEROLEH' : 'TERKUNCI' }}
                    </span>

                    <h3>{{ $badge['name'] }}</h3>

                    <p>{{ $badge['desc'] }}</p>

                    @if ($badge['earned'] && $badge['earned_at'])
                        <div class="hn-badge-v8__date">
                            ✨ Diraih
                            {{ \Illuminate\Support\Carbon::parse($badge['earned_at'])->locale('id')->translatedFormat('j F Y') }}
                        </div>
                    @endif

                </div>

                <span class="hn-badge-v8__decoration" aria-hidden="true">
                    ✦
                </span>

            </div>

        @endforeach

    </div>
</section>

        <section class="pk-block hn-gallery-section" id="hasil-karya">
            <div class="hn-section-heading">
                <div>
                    <p class="hn-eyebrow">PORTOFOLIO KREATIF</p>
                    <h2 class="pk-h2">Hasil Karya <span class="hn-total">{{ $works->count() }}</span></h2>
                    <p class="hn-subtitle">Semua karya yang kamu bagikan dari Beranda maupun Jelajah ada di sini.</p>
                </div>
                <a class="hn-soft-link" href="{{ route('explore') }}">Jelajahi course &rarr;</a>
            </div>
            @if($works->isNotEmpty())
                <div class="hn-works-grid">
                    @foreach($works as $work)
                        <a class="hn-portfolio-card" href="{{ $work['url'] }}" aria-label="Lihat karya {{ $work['title'] }}">
                            <div class="hn-portfolio-media">
                                
                            @if($work['cover'])
                                <img
                                    src="{{ $work['cover'] }}"
                                    alt="Foto hasil karya {{ $work['title'] }}"
                                    loading="lazy"
                                    onerror="this.hidden=true; this.nextElementSibling.hidden=false"
                                >

                                <span class="hn-image-error" hidden>
                                    Foto tidak ditemukan.
                                    Buka karya dan unggah ulang foto.
                                </span>
                            @else
                                <div class="hn-portfolio-empty">
                                    @include('packs._icon', ['name' => 'image'])
                                </div>
                            @endif

                            </div>
                            <div class="hn-portfolio-copy">
                                <p class="hn-work-category">{{ $work['course'] }}</p>
                                <h3>{{ $work['title'] }}</h3>
                                @if($work['description'])
                                    <p class="hn-work-description">{{ \Illuminate\Support\Str::limit($work['description'], 95) }}</p>
                                @endif
                                <div class="hn-work-footer"><span>{{ $work['date']?->locale('id')->translatedFormat('d M Y') }}</span><strong>Lihat karya &rarr;</strong></div>
                            </div>
                        </a>
                    @endforeach
                </div>
            @else
                <div class="hn-works-empty">
                    <div class="hn-works-empty-icon">@include('packs._icon', ['name' => 'image'])</div>
                    <h3>Belum ada karya yang dibagikan</h3>
                    <p>Selesaikan proyek atau course dan unggah hasilnya. Karyamu akan tampil di sini.</p>
                    <a class="btn btn--primary" href="{{ route('roadmap.index') }}">Mulai berkarya</a>
                </div>
            @endif
        </section>

        <section class="pk-block">
            <h2 class="pk-h2">Draft karya ({{ $drafts->count() }})</h2>
            @if ($drafts->isNotEmpty())
                <p class="pk-muted">Foto yang baru disimpan sebagai draft belum termasuk proyek selesai. Buka kembali untuk mengirim hasil akhir.</p>
                <div class="pk-done">
                    @foreach ($drafts as $draft)
                        <article class="pk-done__card">
                            <div class="pk-done__media">
                                @if ($draft->photos->isNotEmpty())
                                    <img src="{{ $draft->photos->first()->url }}" alt="Foto draft {{ $draft->title ?: $draft->project->title }}" loading="lazy">
                                @endif
                            </div>
                            <div class="pk-done__body">
                                <h3>{{ $draft->title ?: $draft->project->title }}</h3>
                                <p>Belum dikirim · {{ $draft->photos->count() }} foto</p>
                                @if ($draft->project->hobby)
                                    <a class="pk-link" href="{{ route('roadmap.submission.show', $draft->project->hobby->slug) }}">Lanjutkan unggahan</a>
                                @endif
                            </div>
                        </article>
                    @endforeach
                </div>
            @else
                <p class="pk-muted">Tidak ada draft tersimpan.</p>
            @endif
        </section>
    </div>
</div>
@endsection
