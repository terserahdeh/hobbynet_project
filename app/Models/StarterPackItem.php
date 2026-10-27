<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StarterPackItem extends Model
{
    protected $fillable = [
        'starter_pack_id', 'name', 'description', 'price',
        'is_essential', 'image', 'shop_url', 'position',
    ];

    protected $casts = [
        'is_essential' => 'boolean',
        'price'        => 'integer',
    ];

    public function pack(): BelongsTo
    {
        return $this->belongsTo(StarterPack::class, 'starter_pack_id');
    }

    public function getPriceLabelAttribute(): string
    {
        return $this->price > 0 ? '~' . StarterPack::rupiah($this->price) : 'Tanpa biaya';
    }
}
