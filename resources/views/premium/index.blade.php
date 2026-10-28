@extends('layouts.app')

@section('title', 'HobbyNet Plus')

@section('content')
@php
    $rp = fn ($n) => 'Rp ' . number_format($n, 0, ',', '.');
@endphp

<div class="plus">

    @if (session('status'))
        <div role="status" class="flash flash--ok">
            {{ session('status') }}
        </div>
    @endif

    <header class="plus__hero">
        <p class="eyebrow">✦ HOBBYNET PLUS</p>

        <h1 class="plus__title">
            Lebih Banyak Hobi,<br>
            Lebih Banyak Karya.
        </h1>

        <p class="plus__lead">
            Mulai perjalanan kreatifmu secara gratis.
            Upgrade ke HobbyNet Plus untuk membuka
            course premium dan belajar lebih jauh
            melalui proyek-proyek kreatif pilihan.
        </p>

        @if ($isPlus && $user?->plus_until)
            <p class="pill pill--plus plus__status">
                ✦ Plus aktif sampai
                {{ $user->plus_until->locale('id')->translatedFormat('j F Y') }}
            </p>
        @endif
    </header>

    <div class="plans">

        {{-- Paket Gratis --}}
        <article class="plan">
            <h2 class="plan__name">Gratis</h2>

            <p class="plan__price">
                <b>Rp 0</b>
                <span>selamanya</span>
            </p>

            <ul class="plan__list">
                <li>Akses course gratis</li>
                <li>Roadmap pembelajaran bertahap</li>
                <li>Checklist alat dan bahan</li>
                <li>Upload hasil karya ke profil</li>
                <li>Kumpulkan lencana pencapaian</li>
            </ul>

            @auth
                <span class="plan__note">
                    {{ $isPlus ? 'Fitur dasar tetap tersedia' : 'Paket kamu sekarang' }}
                </span>
            @else
                <a class="btn btn--ghost"
                   href="{{ route('onboarding.account') }}">
                    Mulai Gratis
                </a>
            @endauth
        </article>

        {{-- Paket Plus Bulanan --}}
        <article class="plan plan--plus">
            <h2 class="plan__name">Plus Bulanan</h2>

            <p class="plan__price">
                <b>{{ $rp($plans['monthly']['price']) }}</b>
                <span>per bulan</span>
            </p>

            <ul class="plan__list">
                <li>Semua fitur paket Gratis</li>
                <li>Akses course premium</li>
                <li>Materi dan latihan course premium</li>
                <li>Roadmap dan progres course premium</li>
                <li>Lencana Plus Member</li>
            </ul>

            @auth
                <form method="POST"
                      action="{{ route('plus.subscribe') }}">
                    @csrf
                    <input type="hidden"
                           name="plan"
                           value="monthly">

                    <button type="submit"
                            class="btn btn--primary">
                        {{ $isPlus ? 'Perpanjang 1 Bulan' : 'Pilih Bulanan' }}
                    </button>
                </form>
            @else
                <a class="btn btn--primary"
                   href="{{ route('onboarding.account') }}">
                    Daftar Dulu
                </a>
            @endauth
        </article>

        {{-- Paket Plus Tahunan --}}
        <article class="plan plan--plus plan--best">
            <span class="plan__ribbon">
                Hemat {{ $yearlySaving }}%
            </span>

            <h2 class="plan__name">Plus Tahunan</h2>

            <p class="plan__price">
                <b>{{ $rp($plans['yearly']['price']) }}</b>
                <span>
                    per tahun · setara
                    {{ $rp((int) round($plans['yearly']['price'] / 12)) }}
                    per bulan
                </span>
            </p>

            <ul class="plan__list">
                <li>Semua fitur Plus Bulanan</li>
                <li>Akses course premium selama masa aktif</li>
                <li>Progres pembelajaran tetap tersimpan</li>
                <li>Harga lebih hemat dibandingkan bulanan</li>
                <li>Lencana Plus Member</li>
            </ul>

            @auth
                <form method="POST"
                      action="{{ route('plus.subscribe') }}">
                    @csrf
                    <input type="hidden"
                           name="plan"
                           value="yearly">

                    <button type="submit"
                            class="btn btn--primary">
                        {{ $isPlus ? 'Perpanjang 1 Tahun' : 'Pilih Tahunan' }}
                    </button>
                </form>
            @else
                <a class="btn btn--primary"
                   href="{{ route('onboarding.account') }}">
                    Daftar Dulu
                </a>
            @endauth
        </article>

    </div>

    <p class="plus__demo">
        Mode demo: aktivasi paket Plus masih disimulasikan.
        Belum ada pembayaran uang sungguhan.
    </p>

    <section class="compare" aria-labelledby="h-compare">
        <h2 class="plus__h2" id="h-compare">
            Bandingkan Paket
        </h2>

        <div class="compare__table" role="table">
            <div class="compare__row compare__row--head" role="row">
                <span>Fitur</span>
                <span>Gratis</span>
                <span>Plus</span>
            </div>

            <div class="compare__row" role="row">
                <span>Course gratis</span>
                <span>✓</span>
                <span>✓</span>
            </div>

            <div class="compare__row" role="row">
                <span>Course premium</span>
                <span>—</span>
                <span>✓</span>
            </div>

            <div class="compare__row" role="row">
                <span>Roadmap dan checklist</span>
                <span>✓</span>
                <span>✓</span>
            </div>

            <div class="compare__row" role="row">
                <span>Upload portofolio karya</span>
                <span>✓</span>
                <span>✓</span>
            </div>

            <div class="compare__row" role="row">
                <span>Lencana pencapaian</span>
                <span>✓</span>
                <span>✓</span>
            </div>

            <div class="compare__row" role="row">
                <span>Lencana Plus Member</span>
                <span>—</span>
                <span>✓</span>
            </div>
        </div>
    </section>

    <section class="upnext" aria-labelledby="h-benefits">
        <h2 class="plus__h2" id="h-benefits">
            Mengapa Memilih Plus?
        </h2>

        <ul class="upnext__list">
            <li>
                <b>Belajar lebih banyak.</b>
                Jelajahi course premium dengan
                materi dan latihan yang lebih beragam.
            </li>
            <li>
                <b>Bangun portofoliomu.</b>
                Simpan hasil karya dan dokumentasikan
                perjalanan belajarmu.
            </li>
        </ul>

        <a href="{{ auth()->check() ? route('explore') : route('onboarding.account') }}"
           class="btn btn--ghost"
           style="margin-top:20px">
            Jelajahi Course
        </a>
    </section>

    @if ($isPlus)
        <form class="plus__cancel"
              method="POST"
              action="{{ route('plus.cancel') }}"
              onsubmit="return confirm('Hentikan langganan Plus?')">
            @csrf

            <button type="submit" class="link-quiet link-btn">
                Hentikan Langganan
            </button>
        </form>
    @endif

</div>
@endsection
