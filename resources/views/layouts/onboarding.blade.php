<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'HobbyNet')</title>
    <link rel="icon" type="image/png" href="{{ asset('favicon.png') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/onboarding.css') }}">
    @stack('head')
</head>
<body>
    <a class="skip-link" href="#konten">Langsung ke konten</a>

    <header class="topbar">
        <a href="{{ url('/') }}" class="brand" aria-label="HobbyNet, beranda">
            <span class="brand__mark" aria-hidden="true">
                <svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="9"/><circle cx="8" cy="11" r="1"/><circle cx="12" cy="7.5" r="1"/><circle cx="16" cy="11" r="1"/><path d="M13 21a2 2 0 0 1 0-4h1.5a2 2 0 0 0 0-4"/></svg>
            </span>
            <span class="brand__name">HobbyNet</span>
        </a>
        <p class="topbar__step">Langkah {{ $step ?? 1 }} dari 4</p>
    </header>

    @php
        $current = $step ?? 1;
        $labels  = [
            1 => ['Mulai di sini', 'Perjalananmu', 'left'],
            2 => ['Perjalananmu', 'Personalisasi', 'right'],
            3 => ['Personalisasi', 'Pilih hobi', 'right'],
        ];
        [$labelLeft, $labelRight, $labelActive] = $labels[$current] ?? $labels[1];
        $palette = ['pink', 'peri', 'sage', 'pink'];
    @endphp
    <nav class="progress" aria-label="Kemajuan pendaftaran">
        <div class="progress__labels">
            <span @class(['is-active' => $labelActive === 'left'])>{{ $labelLeft }}</span>
            <span @class(['is-active' => $labelActive === 'right'])>{{ $labelRight }}</span>
        </div>
        <div class="progress__track" role="progressbar" aria-valuemin="1" aria-valuemax="4" aria-valuenow="{{ $current }}" aria-label="Langkah {{ $current }} dari 4">
            @for ($i = 1; $i <= 4; $i++)
                <span class="progress__seg progress__seg--{{ $palette[$i - 1] }} {{ $i <= $current ? 'is-done' : '' }}"></span>
            @endfor
        </div>
    </nav>

    <main id="konten" class="stage">
        @yield('content')
    </main>

    <footer class="trust">
        <span>
            <svg viewBox="0 0 24 24" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><rect x="5" y="11" width="14" height="9" rx="2"/><path d="M8 11V8a4 4 0 0 1 8 0v3"/></svg>
            Aman dan privat
        </span>
        <span>
            <svg viewBox="0 0 24 24" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M12 21s-7-4.5-9.5-9A5.5 5.5 0 0 1 12 6a5.5 5.5 0 0 1 9.5 6c-2.5 4.5-9.5 9-9.5 9Z"/></svg>
            Dikurasi dengan sepenuh hati
        </span>
    </footer>
    @stack('scripts')
    <script>
        document.querySelectorAll('[data-toggle-password]').forEach(function (btn) {
            btn.addEventListener('click', function () {
                var input = document.getElementById(btn.getAttribute('aria-controls'));
                var show = input.type === 'password';
                input.type = show ? 'text' : 'password';
                btn.textContent = show ? 'Sembunyikan' : 'Tampilkan';
                btn.setAttribute('aria-pressed', show);
            });
        });
    </script>
</body>
</html>
