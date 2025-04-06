<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

use Laravel\Sanctum\HasApiTokens;
use Carbon\Carbon;
use Illuminate\Contracts\Auth\MustVerifyEmail;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'active',
        'active_to',
        'remember_token',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    protected $attributes = [
        'active' => true, // Default value
    ];

    protected static function boot()
    {
        parent::boot();

        // Automatically check active status when retrieving a user
        static::retrieved(function ($user) {
            $user->checkAndUpdateStatus();
        });
    }

    /**
     * Check if the user is active based on the active_to date
     */
    public function isActive()
    {
        return $this->active && $this->active_to && Carbon::parse($this->active_to)->isFuture();
    }

    /**
     * Automatically deactivate user if active_to date has passed
     */
    public function checkAndUpdateStatus()
    {
        if ($this->active_to && Carbon::parse($this->active_to)->isPast()) {
            $this->update(['active' => 0]);
        }
    }

    public function items()
{
    return $this->hasMany(Item::class);
}

public function orders()
{
    return $this->hasMany(Order::class);
}


}


