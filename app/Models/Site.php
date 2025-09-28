<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Site extends Model {
    protected $fillable = [
        'type_id',
        'name',
        'address',
        'lat',
        'lng',
        'geofence',
    ];

    public function type() {
        return $this->belongsTo(Type::class, 'type_id');
    }


    // Add profiles relationship
    public function profiles() {
        return $this->hasMany(Profile::class, 'site_id', 'id');
    }

    // Optionally, all users assigned to this site via profiles
    public function users() {
        return $this->hasManyThrough(User::class, Profile::class, 'site_id', 'id', 'id', 'user_id');
    }
}
