{{-- Hobi Lainnya: roadmap hobi yang belum dipilih. Data dari App\View\Composers\OtherHobbiesComposer ($moreHobbies). --}}
@if ($moreHobbies->isNotEmpty())
<link rel="stylesheet" href="{{ asset('css/other-hobbies.css') }}">
<section class="oh" aria-labelledby="h-oh">
    <div class="oh__head">
        <h2 class="oh__title" id="h-oh">Hobi Lainnya</h2>
        <a class="oh__all" href="{{ route('explore') }}">Lihat semua</a>
    </div>
    <p class="oh__sub">Penasaran dengan hal baru? Coba satu proyek kecil dulu, kamu bisa berhenti kapan pun.</p>

    <ul class="oh__grid">
        @foreach ($moreHobbies as $item)
            @php
                $hobby = $item['hobby'];
                $project = $item['project'];
            @endphp
            <li>
                <article class="oh-card">
                    <a class="oh-card__media oh-card__media--{{ $hobby->color }}"
                       href="{{ route('roadmap.show', $hobby->slug) }}" tabindex="-1" aria-hidden="true">
                        @if ($project->image)
                            <img src="{{ asset($project->image) }}" alt="" loading="lazy" onerror="this.remove()">
                        @endif
                        <span class="oh-card__tag">{{ $hobby->short_name ?? $hobby->name }}</span>
                    </a>
                    <div class="oh-card__body">
                        <h3 class="oh-card__title">{{ $project->title }}</h3>
                        <p class="oh-card__meta">{{ $item['steps'] }} checkpoint &bull; sekitar {{ $item['hours'] }} jam</p>

                        @if ($item['percent'] > 0)
                            <div class="oh-bar" role="progressbar" aria-valuemin="0" aria-valuemax="100"
                                 aria-valuenow="{{ $item['percent'] }}" aria-label="Progres {{ $hobby->name }}">
                                <span style="width: {{ $item['percent'] }}%"></span>
                            </div>
                        @endif

                        <a class="oh-card__cta" href="{{ route('roadmap.show', $hobby->slug) }}">
                            {{ $item['percent'] > 0 ? 'Lanjutkan roadmap' : 'Lihat roadmap' }}
                        </a>
                    </div>
                </article>
            </li>
        @endforeach
    </ul>
</section>
@endif
