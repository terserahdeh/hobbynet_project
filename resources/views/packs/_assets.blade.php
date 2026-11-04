{{-- CSS khusus Jelajahi / Detail / Tersimpan / Profil. Masuk ke @stack('head') di layout. --}}
@push('head')
    <link rel="stylesheet" href="{{ asset('css/packs.css') }}?v={{ filemtime(public_path('css/packs.css')) }}">
@endpush
