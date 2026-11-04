{{-- Ilustrasi cadangan untuk halaman langkah. Usage: @include('roadmap.partials.art', ['hobby' => $hobby]) --}}
@switch($hobby->slug)
    @case('painting')
        <svg viewBox="0 0 120 120" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
            <path d="M86 16a8 8 0 0 1 12 12L58 74l-16 4 4-16z"/>
            <path d="M42 78c-2 9-8 14-18 14 7-3 6-10 6-14"/>
            <path d="M24 100h72"/>
            <circle cx="30" cy="48" r="5"/><circle cx="46" cy="32" r="5"/>
        </svg>
        @break
    @case('photography')
        <svg viewBox="0 0 120 120" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
            <path d="M18 40h18l7-12h34l7 12h18v56H18z"/>
            <circle cx="60" cy="68" r="17"/>
            <circle cx="60" cy="68" r="7"/>
            <path d="M92 52h4"/>
        </svg>
        @break
    @default
        <svg viewBox="0 0 120 120" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
            <circle cx="52" cy="72" r="24"/>
            <path d="M30 66c14 8 30 8 44 0"/>
            <path d="M32 80c12 7 26 7 40 0"/>
            <path d="M44 50c-3 14 0 36 8 46"/>
            <path d="M70 58 100 28"/>
            <path d="M96 24c4-2 9 3 6 7"/>
        </svg>
@endswitch
