@extends('layouts.onboarding', ['step' => 1])

@section('title', 'Masuk · HobbyNet')

@section('content')
<div class="split split--single">
    <section class="card" aria-labelledby="judul-login">
        <h2 id="judul-login">Masuk ke akunmu</h2>
        <form method="POST" action="{{ route('login.store') }}" novalidate>
            @csrf
            <div class="field">
                <label for="email">Alamat email</label>
                <input id="email" name="email" type="email" value="{{ old('email') }}" autocomplete="email" required autofocus
                       @error('email') aria-invalid="true" aria-describedby="email-error" @enderror>
                @error('email') <p class="field__error" id="email-error">{{ $message }}</p> @enderror
            </div>
            <div class="field">
                <label for="password">Kata sandi</label>
                <div class="field__control">
                    <input id="password" name="password" type="password" autocomplete="current-password" required>
                    <button type="button" class="field__toggle" data-toggle-password aria-controls="password" aria-pressed="false">Tampilkan</button>
                </div>
            </div>
            <label class="check"><input type="checkbox" name="remember" value="1"> Ingat saya di perangkat ini</label>
            <button type="submit" class="btn btn--primary">Masuk</button>
        </form>
        <p class="card__login">Belum punya akun? <a href="{{ route('onboarding.account') }}">Buat akun</a></p>
    </section>
</div>
@endsection
