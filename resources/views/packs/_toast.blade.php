{{-- Pemberitahuan kecil setelah simpan / batal simpan. Taruh di dalam <div class="pk">. --}}
@if (session('bookmark'))
    @php($b = session('bookmark'))
    <div class="pk-toast" role="status" data-toast>
        @if ($b['saved'])
            <span>“{{ $b['title'] }}” disimpan.</span>
            <a href="{{ route('bookmarks') }}">Lihat Tersimpan</a>
        @else
            <span>Dihapus dari Tersimpan.</span>
        @endif
    </div>
    @push('scripts')
        <script>
            setTimeout(function () {
                var t = document.querySelector('[data-toast]');
                if (t) t.classList.add('is-hidden');
            }, 5000);
        </script>
    @endpush
@endif
