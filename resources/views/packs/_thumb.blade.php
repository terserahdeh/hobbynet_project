{{-- Gambar dengan placeholder pastel + ikon hobi. Jika file gambar tidak ada, gambar dibuang otomatis.
     Butuh: $color, $icon (slug hobi), $image (path relatif ke public/, boleh kosong) --}}
<span class="pk-thumb pk-thumb--{{ $color ?? 'pink' }}">
    <span class="pk-thumb__icon" aria-hidden="true">
        @include('onboarding.partials.hobby-icon', ['slug' => $icon ?? ''])
    </span>
    @if (! empty($image))
        <img src="{{ asset($image) }}" alt="" loading="lazy" onerror="this.remove()">
    @endif
</span>
