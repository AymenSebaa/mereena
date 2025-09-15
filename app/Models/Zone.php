<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Zone extends Model {
    use HasFactory;

    protected $fillable = [
        'status',
        'name',
        'type_id',
        'location',
        'geofence',
    ];

    protected $casts = [
        'status' => 'boolean',
        'geofence' => 'array',
    ];

    public function type() {
        return $this->belongsTo(Type::class, 'type_id');
    }

    public function hotelZones() {
        return $this->hasMany(HotelZone::class);
    }

    public function hotels() {
        return $this->belongsToMany(Hotel::class, 'hotel_zone');
    }
}
