{{-- Kartu satu paket. Dipakai di Jelajahi dan Tersimpan. Butuh: $pack (dengan project & items), $savedIds --}}
@php
    $project = $pack->project;
    $saved   = in_array($pack->id, $savedIds ?? []);
@endphp
<article class="pk-card">
    <div class="pk-card__media">
        @include('packs._thumb', ['color' => $project->color, 'icon' => $project->icon, 'image' => $project->image])
    </div>

    <form class="pk-card__save" method="POST" action="{{ route('bookmarks.toggle', $pack) }}">
        @csrf
        <button type="submit" class="pk-save {{ $saved ? 'is-saved' : '' }}"
                aria-label="{{ $saved ? 'Hapus dari Tersimpan' : 'Simpan untuk nanti' }}">
            @include('packs._icon', ['name' => 'bookmark'])
        </button>
    </form>

    <div class="pk-card__body">
        <span class="pk-tag pk-tag--{{ $project->color }}">{{ $project->category }} • {{ $project->level_label }}</span>
        @if($pack->is_premium)<span style="display:inline-block;background:#f4d99a;color:#664813;border-radius:30px;padding:4px 10px;font-size:12px;font-weight:bold;margin-top:8px">★ Plus · Premium</span>@else<span style="display:inline-block;background:#e4f0e8;color:#345941;border-radius:30px;padding:4px 10px;font-size:12px;margin-top:8px">Gratis</span>@endif
        <h3 class="pk-card__title"><a href="{{ route('packs.show', $pack) }}">{{ $project->title }}</a></h3>
        <p class="pk-card__sum">{{ \Illuminate\Support\Str::limit($project->summary ?? $project->description, 110) }}</p>

        <ul class="pk-meta">
            <li>@include('packs._icon', ['name' => 'clock']) ~{{ $pack->prep_minutes }} mnt persiapan</li>
            <li>@include('packs._icon', ['name' => 'wallet']) {{ $pack->budget_label }}</li>
        </ul>
    </div>
</article>
