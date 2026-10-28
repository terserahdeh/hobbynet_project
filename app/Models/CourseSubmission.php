<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CourseSubmission extends Model
{
    protected $fillable = ['user_id', 'starter_pack_id', 'title', 'reflection', 'photos', 'status', 'submitted_at'];
    protected function casts(): array { return ['photos' => 'array', 'submitted_at' => 'datetime']; }
    public function starterPack(): BelongsTo { return $this->belongsTo(StarterPack::class); }
}
