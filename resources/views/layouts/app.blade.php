<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'HobbyNet')</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Fraunces:opsz,wght@9..144,600;9..144,700&family=Quicksand:wght@500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/home.css') }}">
    <link rel="icon" type="image/png" href="{{ asset('favicon.png') }}">
    @stack('head')
</head>
<body>
    <a class="skip-link" href="#konten">Langsung ke konten</a>

    <header class="nav">
        <div class="nav__inner">
            <a href="{{ route('home') }}" class="brand" aria-label="HobbyNet, beranda">
                <span class="brand__mark" aria-hidden="true">
                    <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="9"/><circle cx="8" cy="11" r="1"/><circle cx="12" cy="7.5" r="1"/><circle cx="16" cy="11" r="1"/><path d="M13 21a2 2 0 0 1 0-4h1.5a2 2 0 0 0 0-4"/></svg>
                </span>
                <span class="brand__name">HobbyNet</span>
            </a>

            <nav class="nav__links" aria-label="Navigasi utama">
                <a href="{{ route('home') }}" @class(['is-active' => request()->routeIs('home')]) @if (request()->routeIs('home')) aria-current="page" @endif>Beranda</a>
                <a href="{{ route('explore') }}" @class(['is-active' => request()->routeIs('explore')]) @if (request()->routeIs('explore')) aria-current="page" @endif>Jelajahi</a>
                <a href="{{ route('bookmarks') }}" @class(['is-active' => request()->routeIs('bookmarks')]) @if (request()->routeIs('bookmarks')) aria-current="page" @endif>Tersimpan</a>
            </nav>

            <form class="nav__search" role="search" action="{{ route('search') }}" method="GET">
                <label class="sr-only" for="q">Cari proyek</label>
                <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" aria-hidden="true"><circle cx="11" cy="11" r="7"/><path d="m20 20-3.5-3.5"/></svg>
                <input id="q" name="q" type="search" placeholder="Cari proyek…" value="{{ request('q') }}">
            </form>

            <details class="account">
                <summary aria-label="Menu akun">{{ \Illuminate\Support\Str::upper(\Illuminate\Support\Str::substr(auth()->user()->name, 0, 1)) }}</summary>
                <div class="account__menu">
                    <p class="account__name">{{ auth()->user()->name }}</p>
                    <p class="account__mail">{{ auth()->user()->email }}</p>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="account__out">Keluar</button>
                    </form>
                </div>
            </details>
        </div>
    </header>

    <main id="konten" class="page">
        @yield('content')
    </main>

    @stack('scripts')
</body>
</html>
