<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Geofence extends Model {
    protected $fillable = [
        'external_id',
        'name',
        'type',
        'points'
    ];
}
