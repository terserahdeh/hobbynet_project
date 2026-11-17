{{-- CSS khusus Jelajahi / Detail / Tersimpan / Profil. Masuk ke @stack('head') di layout. --}}
@push('head')
    <link rel="preload" href="{{ asset('css/home.css') }}?v={{ filemtime(public_path('css/home.css')) }}" as="style">
    <link rel="stylesheet" href="{{ asset('css/packs.css') }}?v={{ filemtime(public_path('css/packs.css')) }}">
@endpush
