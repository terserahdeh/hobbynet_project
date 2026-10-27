<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DailyActivity extends Model
{
    protected $fillable = ['user_id', 'date', 'minutes'];

    protected $casts = [
        'minutes' => 'integer',
    ];
}
