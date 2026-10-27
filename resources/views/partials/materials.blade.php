{{-- Variabel: $materials (dari StepBlueprints::materialsFor) --}}
<div class="mats">
    <ul class="mats__list">
        @foreach ($materials['items'] as $m)
            <li class="mat {{ $m['owned'] ? 'mat--owned' : '' }}">
                <span class="mat__name">
                    {{ $m['name'] }}
                    @if ($m['note']) <small>{{ $m['note'] }}</small> @endif
                </span>
                <span class="mat__price">{{ $m['owned'] ? 'Sudah punya' : $m['price_label'] }}</span>
            </li>
        @endforeach
    </ul>
    <p class="mats__total">
        @if ($materials['total'] > 0)
            Perkiraan biaya yang perlu disiapkan: <strong>{{ $materials['total_label'] }}</strong> (anggaran {{ $materials['tier_label'] }}).
        @else
            Tidak ada biaya tambahan yang perlu disiapkan.
        @endif
    </p>
    <p class="mats__note">Harga hanya perkiraan kasar.</p>
</div>
