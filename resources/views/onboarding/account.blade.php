@extends('layouts.onboarding', ['step' => 1])

@section('title', 'Buat akun · HobbyNet')

@section('content')
<div class="split">
    <section class="intro">
        <h1>Selamat datang! Yuk, mulai perjalanan kreatifmu.</h1>
        <p class="intro__lead">
            Setiap karya berawal dari satu percikan kecil.
            Ceritakan sedikit tentang dirimu agar kami bisa menyesuaikan pengalamanmu.
        </p>
        <figure class="intro__photo">
            <img src="{{ asset('images/onboarding-studio.jpg') }}"
                 alt="Sudut studio melukis dengan kuda-kuda, kuas, dan cat air di dekat jendela"
                 onerror="this.remove()">
        </figure>
    </section>

    <section class="card" aria-labelledby="judul-form">
        <h2 id="judul-form">Buat akunmu</h2>

        <form method="POST" action="{{ route('onboarding.account.store') }}" novalidate>
            @csrf

            <div class="field">
                <label for="name">Nama lengkap</label>
                <input id="name" name="name" type="text" value="{{ old('name') }}"
                       placeholder="Alex Rivera" autocomplete="name" required autofocus
                       @error('name') aria-invalid="true" aria-describedby="name-error" @enderror>
                @error('name') <p class="field__error" id="name-error">{{ $message }}</p> @enderror
            </div>

            <div class="field">
                <label for="email">Alamat email</label>
                <input id="email" name="email" type="email" value="{{ old('email') }}"
                       placeholder="alex@example.com" autocomplete="email" required
                       @error('email') aria-invalid="true" aria-describedby="email-error" @enderror>
                @error('email') <p class="field__error" id="email-error">{{ $message }}</p> @enderror
            </div>

            <div class="field">
                <label for="password">Kata sandi</label>
                <div class="field__control">
                    <input id="password" name="password" type="password"
                           placeholder="Minimal 8 karakter" autocomplete="new-password" required
                           aria-describedby="password-hint @error('password') password-error @enderror"
                           @error('password') aria-invalid="true" @enderror>
                    <button type="button" class="field__toggle" data-toggle-password aria-controls="password" aria-pressed="false">Tampilkan</button>
                </div>
                <p class="field__hint" id="password-hint">Gunakan huruf dan angka.</p>
                @error('password') <p class="field__error" id="password-error">{{ $message }}</p> @enderror
            </div>

            <button type="submit" class="btn btn--primary">Buat akunku</button>
        </form>

        <p class="card__login">Sudah punya akun? <a href="{{ route('login') }}">Masuk</a></p>

        @if (config('services.google.client_id'))
        <div class="divider"><span>atau daftar dengan</span></div>
        <div class="social">            
            <a class="btn btn--ghost" href="{{ route('social.redirect', 'google') }}">
                <svg viewBox="0 0 24 24" width="25" height="18" aria-hidden="true"><path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92a5.06 5.06 0 0 1-2.2 3.32v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.1z"/><path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/><path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l3.66-2.84z"/><path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z"/></svg>
                <span>Google</span>
            </a>
        </div>
        @endif
    </section>
</div>
@endsection
