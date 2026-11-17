@extends('layouts.app')

@section('title', 'Tersimpan · HobbyNet')

@section('content')
@include('packs._assets')

<div class="pk">
    @include('packs._toast')

    <header class="pk-head">
        <h1 class="pk-title">Tersimpan</h1>
        <p class="pk-sub">Tempat menyimpan paket proyek yang menarik perhatianmu dari Beranda dan Jelajahi. Kapan pun siap, kamu bisa menemukannya lagi di sini.</p>
    </header>

    @if ($packs->isEmpty())
        <div class="empty pk-empty-gap">
            <p>Masih kosong, nih! Temukan proyek seru di Beranda atau Jelajahi, lalu tekan ikon simpan pada paket yang kamu suka. Semua pilihanmu akan tersimpan di sini, siap dicoba kapan saja.</p>
            <a class="btn btn--primary pk-explore-cta" href="{{ route('explore') }}">Jelajahi paket</a>
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
