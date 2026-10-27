<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Project extends Model
{
    public const LEVELS = ['beginner', 'intermediate', 'advanced'];

    protected $fillable = [
        'hobby_id', 'slug', 'title', 'category', 'summary', 'skill', 'description', 'level',
        'duration_minutes', 'learners_count', 'image', 'icon', 'color', 'is_starter', 'is_popular',
    ];

    protected $casts = [
        'is_starter' => 'boolean',
        'is_popular' => 'boolean',
    ];

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public function hobby(): BelongsTo
    {
        return $this->belongsTo(Hobby::class);
    }

    public static function levelLabel(string $level): string
    {
        return match ($level) {
            'intermediate' => 'Menengah',
            'advanced'     => 'Mahir',
            default        => 'Pemula',
        };
    }

    public static function levelRank(string $level): int
    {
        $rank = array_search($level, self::LEVELS, true);

        return $rank === false ? 0 : $rank;
    }

    public function getLevelLabelAttribute(): string
    {
        return static::levelLabel($this->level);
    }

    public function getDurationLabelAttribute(): string
    {
        return static::humanDuration($this->duration_minutes);
    }

    public function getLearnersLabelAttribute(): string
    {
        $n = $this->learners_count;

        if ($n >= 1000) {
            return rtrim(rtrim(number_format($n / 1000, 1, ',', ''), '0'), ',') . ' rb peserta';
        }

        return $n . ' peserta';
    }

    public static function humanDuration(int $minutes): string
    {
        if ($minutes < 60) {
            return $minutes . ' menit';
        }

        if ($minutes < 1440) {
            return rtrim(rtrim(number_format($minutes / 60, 1, ',', ''), '0'), ',') . ' jam';
        }

        return round($minutes / 1440) . ' hari';
    }
}
