@extends('layouts.app')

@section('title', 'Tersimpan · HobbyNet')

@section('content')
@include('packs._assets')

<div class="pk">
    @include('packs._toast')

    <header class="pk-head">
        <h1 class="pk-title">Tersimpan</h1>
        <p class="pk-sub">Paket yang kamu simpan untuk nanti. Tidak perlu buru-buru, mulai saat kamu siap.</p>
    </header>

    @if ($packs->isEmpty())
        <div class="empty pk-empty-gap">
            <p>Belum ada yang disimpan. Tekan ikon penanda di kartu paket, atau “Simpan untuk Nanti” di halaman detail, dan hasilnya muncul di sini.</p>
            <a class="btn btn--primary" href="{{ route('explore') }}">Jelajahi paket</a>
        </div>
    @else
        <p class="pk-note">{{ $packs->count() }} paket tersimpan</p>
        <div class="pk-grid">
            @foreach ($packs as $pack)
                @include('packs._card', ['pack' => $pack])
            @endforeach
        </div>
    @endif
</div>
@endsection
