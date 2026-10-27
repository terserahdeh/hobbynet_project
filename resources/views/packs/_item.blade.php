{{-- Satu baris perlengkapan. Centang = "aku sudah punya". Butuh: $item, $project --}}
<li class="pk-item {{ $item->is_essential ? '' : 'pk-item--optional' }}">
    <label class="pk-check">
        <input type="checkbox" value="{{ $item->id }}" data-owned
               data-essential="{{ $item->is_essential ? 1 : 0 }}" data-price="{{ $item->price }}">
        <span class="pk-box" aria-hidden="true">@include('packs._icon', ['name' => 'check'])</span>
        <span class="sr-only">Aku sudah punya {{ $item->name }}</span>
    </label>

    <div class="pk-item__media">
        @include('packs._thumb', ['color' => $project->color, 'icon' => $project->icon, 'image' => $item->image])
    </div>

    <div class="pk-item__text">
        <h3>{{ $item->name }}</h3>
        @if ($item->description)
            <p>{{ $item->description }}</p>
        @endif
    </div>

    <div class="pk-item__price">
        <strong>{{ $item->price_label }}</strong>
        @if ($item->shop_url && $item->price > 0)
            <a href="{{ $item->shop_url }}" target="_blank" rel="noopener noreferrer">Lihat produk</a>
        @endif
    </div>
</li>
