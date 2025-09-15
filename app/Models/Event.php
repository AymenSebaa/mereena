<?php


namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Event extends Model {
    protected $fillable = [
        'external_id',
        'user_id',
        'device_id',
        'geofence_id',
        'type',
        'message',
        'detail',
        'address',
        'latitude',
        'longitude',
        'altitude',
        'course',
        'speed',
        'time',
        'additional'
    ];

    protected $casts = [
        'additional' => 'array',
        'time' => 'datetime',
    ];

    public function bus() {
        return $this->belongsTo(Bus::class, 'device_id', 'external_id');
    }

    public function hotel() {
        return $this->belongsTo(Hotel::class, 'geofence_id', 'external_id');
        // or name mapping if geofence_id is null
    }
}
