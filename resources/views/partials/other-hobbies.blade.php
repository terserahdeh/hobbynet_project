{{-- Hobi Lainnya: roadmap hobi/level lain dan proyek berpemandu. Data dari App\View\Composers\OtherHobbiesComposer ($moreItems). --}}
@if ($moreItems->isNotEmpty())
<link rel="stylesheet" href="{{ asset('css/other-hobbies.css') }}">
<section class="oh" aria-labelledby="h-oh">
    <div class="oh__head">
        <h2 class="oh__title" id="h-oh">Hobi Lainnya</h2>
        <a class="oh__all" href="{{ route('explore') }}">Lihat semua</a>
    </div>
    <p class="oh__sub">Roadmap hobi atau tingkat lain, dan proyek berpemandu. Coba kapan pun kamu penasaran.</p>

    <ul class="oh__grid">
        @foreach ($moreItems as $item)
            @php $p = $item['project']; @endphp
            <li>
                <a class="oh-tile" href="{{ $item['href'] }}">
                    <span class="oh-tile__media oh-tile__media--{{ $p->color ?? 'pink' }}">
                        @if ($p->image)
                            <img src="{{ asset($p->image) }}" alt="" loading="lazy" onerror="this.remove()">
                        @endif
                        <span class="oh-tile__tag">{{ $item['kind'] }}</span>
                        @if ($item['premium'])
                            <span class="oh-tile__plus" title="{{ $item['locked'] ? 'Buka dengan HobbyNet Plus' : 'Termasuk paket Plus-mu' }}">&#9733; Plus</span>
                        @endif
                    </span>
                    <span class="oh-tile__hobby">{{ $item['hobby'] }} &bull; {{ $item['level'] }}</span>
                    <strong class="oh-tile__title">{{ $p->title }}</strong>
                    <span class="oh-tile__meta">{{ $item['steps'] }} langkah &bull; &plusmn;{{ $item['duration'] }}</span>
                    @if ($item['percent'] > 0)
                        <span class="oh-bar" role="progressbar" aria-valuemin="0" aria-valuemax="100" aria-valuenow="{{ $item['percent'] }}" aria-label="Progres {{ $p->title }}">
                            <span style="width: {{ $item['percent'] }}%"></span>
                        </span>
                    @endif
                </a>
            </li>
        @endforeach
    </ul>
</section>
@endif
