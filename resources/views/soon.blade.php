@extends('layouts.app')

@section('title', $title . ' · HobbyNet')

@section('content')
<div class="soon">
    <h1 class="dash__title">{{ $title }}</h1>
    <p>Halaman ini sedang disiapkan. Sementara itu, kembali ke beranda dan lanjutkan berkarya.</p>
    <a class="btn btn--primary" href="{{ route('home') }}">Kembali ke beranda</a>
</div>
@endsection
