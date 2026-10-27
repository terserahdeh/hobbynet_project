<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'onboarding_step',
        'experience_level', 
        'tools', 
        'budget', 
        'daily_goal_minutes',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'tools' => 'array',
        ];
    }

    public function hobbies(): BelongsToMany
    {
        return $this->belongsToMany(Hobby::class)->withTimestamps();
    }
    
    public function projectProgress(): HasMany
    {
        return $this->hasMany(UserProject::class);
    }

    public function dailyActivities(): HasMany
    {
        return $this->hasMany(DailyActivity::class);
    }

    /** Rute langkah onboarding yang harus dikerjakan berikutnya. */
    public function nextOnboardingRoute(): string
    {
        return match (true) {
            $this->onboarding_step <= 2 => 'onboarding.personalize',
            $this->onboarding_step === 3 => 'onboarding.hobbies',
            default                      => 'home',
        };
    }
}
