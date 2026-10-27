@extends('layouts.onboarding', ['step' => 3])

@section('title', 'Pilih hobi · HobbyNet')

@section('content')
<div class="split split--wide">
    <section class="card" aria-labelledby="judul-hobi">
        <h1 id="judul-hobi" class="card__title">Hobi apa yang bikin kamu penasaran?</h1>
        <p class="intro__lead">Pilih satu atau lebih. Kamu selalu bisa menjelajah hobi lain nanti.</p>

        <form method="POST" action="{{ route('onboarding.hobbies.store') }}" novalidate>
            @csrf

            <fieldset class="hobbies" @error('hobbies') aria-describedby="hobbies-error" @enderror>
                <legend class="sr-only">Pilih hobi</legend>

                @foreach ($hobbies as $hobby)
                    <label class="hobby hobby--{{ $hobby->color }}">
                        <input type="checkbox" name="hobbies[]" value="{{ $hobby->id }}"
                               @checked(in_array($hobby->id, $selected, true))>
                        <span class="hobby__body">
                            <span class="hobby__icon" aria-hidden="true">
                                @include('partials.hobby-icon', ['slug' => $hobby->slug])
                            </span>
                            <span class="hobby__name">{{ $hobby->name }}</span>
                            <span class="hobby__tagline">{{ $hobby->tagline }}</span>
                            <span class="hobby__project">Proyek pertama: {{ $hobby->first_project }}</span>
                            <span class="hobby__check" aria-hidden="true">
                                <svg viewBox="0 0 24 24" width="14" height="14" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><path d="m5 12 5 5 9-10"/></svg>
                            </span>
                        </span>
                    </label>
                @endforeach
            </fieldset>

            @error('hobbies')
                <p class="field__error" id="hobbies-error" role="alert">{{ $message }}</p>
            @enderror

            <button type="submit" class="btn btn--primary">Lanjut</button>
        </form>

        @if (auth()->user()->onboarding_step < 4)

            <form method="POST" action="{{ route('onboarding.hobbies.skip') }}" class="skip">
                @csrf
                <button type="submit" class="link-btn">Lewati dulu, aku mau lihat-lihat</button>
            </form>

        @endif
    </section>
</div>
@endsection
