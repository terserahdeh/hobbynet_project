{{-- Usage: @include('roadmap.partials.icon', ['name' => 'clock']) --}}
@php
    $paths = [
        'clock' => '<circle cx="12" cy="12" r="9"/><path d="M12 7v5l3 2"/>',
        'target' => '<circle cx="12" cy="12" r="9"/><circle cx="12" cy="12" r="4.5"/><circle cx="12" cy="12" r="1" fill="currentColor"/>',
        'lock' => '<rect x="5" y="11" width="14" height="9" rx="2.5"/><path d="M8 11V8a4 4 0 0 1 8 0v3"/>',
        'check' => '<path d="m5.5 12.5 4.2 4.2L18.5 8"/>',
        'chev-left' => '<path d="m15 6-6 6 6 6"/>',
        'chev-right' => '<path d="m9 6 6 6-6 6"/>',
        'arrow-right' => '<path d="M5 12h14"/><path d="m13 6 6 6-6 6"/>',
        'bulb' => '<path d="M9 18h6"/><path d="M10 21h4"/><path d="M12 3a6 6 0 0 0-3.6 10.8c.7.5 1.1 1.3 1.1 2.2h5c0-.9.4-1.7 1.1-2.2A6 6 0 0 0 12 3z"/>',
        'plus' => '<path d="M12 5v14"/><path d="M5 12h14"/>',
        'close' => '<path d="m6 6 12 12"/><path d="M18 6 6 18"/>',
        'compass' => '<circle cx="12" cy="12" r="9"/><path d="m15.5 8.5-2 5-5 2 2-5z"/>',
        'image' => '<rect x="3" y="4" width="18" height="16" rx="3"/><circle cx="9" cy="10" r="1.6"/><path d="m21 16-5-5-8 9"/>',
        'heart' => '<path d="M12 20s-7-4.6-7-10a4 4 0 0 1 7-2.6A4 4 0 0 1 19 10c0 5.4-7 10-7 10z"/>',
        'yarn' => '<circle cx="12" cy="12" r="9"/><path d="M4 10c5 3 11 3 16 0"/><path d="M4.5 15c5 3 10 3 15 0"/><path d="M9 3.5c-1 5 0 12 3 17"/>',
        'star' => '<path d="m12 3.5 2.6 5.3 5.9.9-4.3 4.1 1 5.8-5.2-2.8-5.2 2.8 1-5.8-4.3-4.1 5.9-.9z"/>',
    ];
@endphp
<svg class="rm-i" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">{!! $paths[$name] ?? '' !!}</svg>
