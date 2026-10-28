<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Collection;

/**
 * Paket persiapan (alat & bahan) untuk satu Project.
 * Judul, ringkasan, kategori, level, warna, ikon, dan gambar diambil dari Project.
 */
class StarterPack extends Model
{
    protected $fillable = ['slug', 'project_id', 'prep_minutes', 'tip', 'is_premium'];

    protected $casts = ['is_premium' => 'boolean'];

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(StarterPackItem::class)->orderBy('position');
    }

    public function getEssentialItemsAttribute(): Collection
    {
        return $this->items->where('is_essential', true)->values();
    }

    public function getOptionalItemsAttribute(): Collection
    {
        return $this->items->where('is_essential', false)->values();
    }

    public function getEssentialTotalAttribute(): int
    {
        return (int) $this->essential_items->sum('price');
    }

    /** Contoh: "Rp 55.000 – 90.000" (utama saja sampai utama + opsional). */
    public function getBudgetLabelAttribute(): string
    {
        $min = $this->essential_total;
        $max = $min + (int) $this->optional_items->sum('price');

        return $min === $max
            ? self::rupiah($min)
            : self::rupiah($min) . ' – ' . number_format($max, 0, ',', '.');
    }

    public static function rupiah(int $amount): string
    {
        return 'Rp ' . number_format($amount, 0, ',', '.');
    }
}
