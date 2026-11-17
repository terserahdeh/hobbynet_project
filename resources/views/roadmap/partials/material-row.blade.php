{{-- One material. Needs $m, $owned, $hobby, $step, $rupiah (from step-materials). --}}

@php $isOwned = in_array($m->id, $owned, true); @endphp
<li class="rmm-item {{ $isOwned ? 'is-owned' : '' }}">
    <span class="rmm-thumb">
        @if (filled($m->image))
            <img src="{{ route('roadmap.steps.materials.image', [$hobby->slug, $step->slug, $m->id]) }}" alt="{{ $m->name }}" loading="lazy">
        @else
            @include('roadmap.partials.icon', ['name' => $m->kind === 'tool' ? 'bulb' : 'plus'])
        @endif
    </span>
    <span class="rmm-item__body">
        <strong>{{ $m->name }}</strong>
        <small>{{ $m->kind === 'tool' ? 'Alat' : 'Bahan' }}@if ($m->spec) · {{ $m->spec }}@endif</small>
        @if ($m->description)<span class="rmm-item__desc">{{ $m->description }}</span>@endif
        @if ($m->item_url)
            <a href="{{ $m->item_url }}" class="rmm-item__link" target="_blank" rel="noopener noreferrer">Lihat produk @include('roadmap.partials.icon', ['name' => 'arrow-right'])</a>
        @endif
    </span>
    <span class="rmm-item__side">
        @if (! is_null($m->price))<b class="rmm-price">{{ $rupiah($m->price) }}</b>@endif
        <label class="rmm-own">
            <input type="checkbox" data-url="{{ route('roadmap.steps.materials.toggle', [$hobby->slug, $step->slug, $m->id]) }}" @checked($isOwned)>
            <span>Sudah punya</span>
        </label>
    </span>
</li>
