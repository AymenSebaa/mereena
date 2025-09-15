<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Hotel extends Model {
    protected $fillable = [
        'external_id',
        'name',
        'address',
        'lat',
        'lng',
        'geofence',
        'stars',
    ];

    public function scans() {
        return $this->hasMany(Scan::class, 'type_id', 'id')
            ->where('type', 'hotels')
            ->with('user'); // eager load user
    }

    public function hotelZones() {
        return $this->hasMany(HotelZone::class);
    }

    public function zones() {
        return $this->belongsToMany(Zone::class, 'hotel_zone');
    }

    // Add profiles relationship
    public function profiles() {
        return $this->hasMany(Profile::class, 'hotel_id', 'id');
    }

    // Optionally, all users assigned to this hotel via profiles
    public function users() {
        return $this->hasManyThrough(User::class, Profile::class, 'hotel_id', 'id', 'id', 'user_id');
    }
}
