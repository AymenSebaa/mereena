<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\BelongsToOrganization;

class Site extends Model {
    use SoftDeletes, BelongsToOrganization;

    protected $fillable = [
        'organization_id',
        'name',
        'address',
        'lat',
        'lng',
        'geofence',
    ];

    // Add profiles relationship
    public function profiles() {
        return $this->hasMany(Profile::class, 'site_id', 'id');
    }

    // Optionally, all users assigned to this site via profiles
    public function users() {
        return $this->hasManyThrough(User::class, Profile::class, 'site_id', 'id', 'id', 'user_id');
    }
}
