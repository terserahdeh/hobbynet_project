@extends('layouts.onboarding', ['step' => 2])

@section('title', 'Personalisasi · HobbyNet')

@push('head')
    <link rel="stylesheet" href="{{ asset('css/personalize.css') }}">
@endpush

@section('content')
<div class="persona">
    <header class="persona__head">
        <h1>Mari sesuaikan HobbyNet dengan tempomu.</h1>
        <p>Ceritakan sedikit latar belakangmu supaya kami bisa merekomendasikan titik awal terbaik.</p>
    </header>

    <form method="POST" action="{{ route('onboarding.personalize.store') }}" novalidate>
        @csrf

        {{-- 01: pengalaman --}}
        <fieldset class="q">
            <legend><span class="q__num" aria-hidden="true">01</span> Seberapa jauh pengalamanmu?</legend>
            <div class="opts opts--3">
                @php
                    $levels = [
                        'beginner'  => ['Pemula total', 'Aku belum pernah mencobanya dan ingin mulai dari nol.'],
                        'some'      => ['Sedikit pengalaman', 'Aku tahu dasarnya, tapi butuh panduan untuk berkembang.'],
                        'returning' => ['Kembali lagi', 'Sudah lama tidak mencoba, aku ingin menekuninya lagi.'],
                    ];
                @endphp
                @foreach ($levels as $value => [$title, $desc])
                    <label class="opt opt--level">
                        <input type="radio" name="experience_level" value="{{ $value }}" @checked($experience === $value)>
                        <span class="opt__body">
                            <span class="opt__title">{{ $title }}</span>
                            <span class="opt__desc">{{ $desc }}</span>
                            <span class="opt__check" aria-hidden="true">
                                <svg viewBox="0 0 24 24" width="12" height="12" fill="none" stroke="currentColor" stroke-width="3.5" stroke-linecap="round" stroke-linejoin="round"><path d="m5 12 5 5 9-10"/></svg>
                            </span>
                        </span>
                    </label>
                @endforeach
            </div>
            @error('experience_level') <p class="field__error" role="alert">{{ $message }}</p> @enderror
        </fieldset>

        {{-- 02: alat --}}
        <fieldset class="q">
            <legend><span class="q__num" aria-hidden="true">02</span> Ada alat di rumah?</legend>
            <div class="opts opts--4">
                @foreach (['paintbrushes' => 'Kuas lukis', 'yarn_hook' => 'Hook rajut', 'camera' => 'Kamera DSLR', 'none' => 'Belum ada'] as $value => $label)
                    <label class="opt opt--tool">
                        <input type="checkbox" name="tools[]" value="{{ $value }}" @checked(in_array($value, $tools, true))>
                        <span class="opt__body">
                            <span class="opt__box" aria-hidden="true">
                                <svg viewBox="0 0 24 24" width="11" height="11" fill="none" stroke="currentColor" stroke-width="4" stroke-linecap="round" stroke-linejoin="round"><path d="m5 12 5 5 9-10"/></svg>
                            </span>
                            <span class="opt__title">{{ $label }}</span>
                        </span>
                    </label>
                @endforeach
            </div>
            @error('tools.*') <p class="field__error" role="alert">{{ $message }}</p> @enderror
        </fieldset>

        {{-- 03: anggaran (opsional) --}}
        <fieldset class="q">
            <legend>
                <span class="q__num" aria-hidden="true">03</span>
                Berapa anggaran proyekmu biasanya? <span class="q__opt">(opsional)</span>
            </legend>
            <div class="opts opts--3">
                @foreach (['budget' => 'Hemat', 'standard' => 'Standar', 'premium' => 'Premium'] as $value => $label)
                    <label class="opt opt--budget">
                        <input type="radio" name="budget" value="{{ $value }}" @checked($budget === $value)>
                        <span class="opt__body"><span class="opt__title">{{ $label }}</span></span>
                    </label>
                @endforeach
            </div>
            @error('budget') <p class="field__error" role="alert">{{ $message }}</p> @enderror
        </fieldset>

        <div class="persona__actions">
            <button type="submit" class="btn btn--primary">Lanjut</button>
        </div>
    </form>

    <form method="POST" action="{{ route('onboarding.personalize.skip') }}" class="skip">
        @csrf
        <button type="submit" class="link-btn">Lewati dulu</button>
    </form>
</div>
@endsection

@push('scripts')
<script>
    (function () {
        // "Belum ada" tidak boleh dipilih bersama alat lain.
        var boxes = document.querySelectorAll('input[name="tools[]"]');
        boxes.forEach(function (box) {
            box.addEventListener('change', function () {
                if (!box.checked) return;
                boxes.forEach(function (other) {
                    if (other !== box && (box.value === 'none' || other.value === 'none')) other.checked = false;
                });
            });
        });

        // Anggaran opsional: klik lagi pilihan yang aktif untuk membatalkannya.
        var radios = document.querySelectorAll('input[name="budget"]');
        radios.forEach(function (r) { r.dataset.was = r.checked ? '1' : '0'; });
        radios.forEach(function (r) {
            r.addEventListener('click', function () {
                if (r.dataset.was === '1') {
                    r.checked = false;
                    r.dataset.was = '0';
                } else {
                    radios.forEach(function (o) { o.dataset.was = '0'; });
                    r.dataset.was = '1';
                }
            });
        });
    })();
</script>
@endpush
