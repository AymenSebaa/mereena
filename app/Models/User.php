<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable {
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'organization_id',
        'name',
        'email',
        'password',
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
    protected function casts(): array {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function pushSubscription() {
        return $this->hasOne(PushSubscription::class);
    }

    public function profile() {
        return $this->hasOne(Profile::class, 'user_id', 'id');
    }

    public function lastOtp() {
        return $this->hasOne(Otp::class, 'email', 'email')->latestOfMany();
    }

    public function scans() {
        return $this->hasMany(Scan::class, 'type_id', 'id')
            ->where('type', 'users')
            ->with('user'); // eager load user
    }

    public function reservations() {
        return $this->hasMany(Reservation::class);
    }

    public function organization() {
        return $this->belongsTo(Organization::class);
    }
}
