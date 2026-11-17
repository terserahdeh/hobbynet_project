{{-- Checklist alat & bahan dari starter pack (sumber yang sama dengan Jelajahi).
     Butuh: $checklist hasil PackChecklist::forProject(). Tidak tampil jika null. --}}
@if ($checklist)
<link rel="stylesheet" href="{{ asset('css/pack-checklist.css') }}">
<section class="pc" aria-labelledby="pc-title-{{ $checklist['pack']->id }}">
    <div class="pc__head">
        <h2 id="pc-title-{{ $checklist['pack']->id }}">Alat &amp; Bahan</h2>
        <span>{{ $checklist['ready'] }} dari {{ $checklist['total'] }} siap</span>
    </div>
    <div class="pc__bar" aria-hidden="true">
        <span style="width: {{ $checklist['total'] ? round($checklist['ready'] / $checklist['total'] * 100) : 0 }}%"></span>
    </div>

    <ul class="pc__list">
        @foreach ($checklist['items'] as $item)
            @php $owned = in_array($item->id, $checklist['owned'], true); @endphp
            <li>
                <form method="POST" action="{{ route('packs.items.owned', $item) }}">
                    @csrf
                    <label class="pc__item {{ $owned ? 'is-owned' : '' }}">
                        <input type="checkbox" @checked($owned) onchange="this.form.submit()">
                        <span class="pc__name">
                            {{ $item->name }}
                            @unless ($item->is_essential) <small>opsional</small> @endunless
                        </span>
                        <span class="pc__price">{{ $owned ? 'Sudah punya' : $item->price_label }}</span>
                    </label>
                    <noscript><button type="submit">Simpan</button></noscript>
                </form>
            </li>
        @endforeach
    </ul>

    <p class="pc__sum">Sisa perkiraan biaya: <strong>{{ \App\Models\StarterPack::rupiah($checklist['remaining']) }}</strong></p>
    <a style="color:white" class="pc__shop" href="{{ route('packs.show', $checklist['pack']) }}">Lihat harga &amp; beli di Jelajahi &rarr;</a>
</section>
@endif