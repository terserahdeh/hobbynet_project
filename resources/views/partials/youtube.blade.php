{{-- Variabel: $url (tautan atau ID YouTube), $title (opsional). Tidak menampilkan apa pun jika tautan tidak valid. --}}
@php $videoId = \App\Support\YouTube::id($url ?? null); @endphp
@if ($videoId)
    <link rel="stylesheet" href="{{ asset('css/roadmap-extras.css') }}">
    <div class="yt">
        <iframe src="{{ \App\Support\YouTube::embedUrl($videoId) }}"
                title="{{ $title ?? 'Tutorial video' }}"
                loading="lazy"
                allow="accelerometer; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share"
                referrerpolicy="strict-origin-when-cross-origin"
                allowfullscreen></iframe>
    </div>
@endif
