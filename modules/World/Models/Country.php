<?php

namespace Modules\World\Models;

use Illuminate\Database\Eloquent\Model;

class Country extends Model {

    protected $fillable = [
        'region_id',
        'name',
        'iso2',
        'iso3',
        'phone_code',
        'currency',
        'emoji',
        'lat',
        'lng',
    ];

    public function region() {
        return $this->belongsTo(Region::class);
    }

    public function states() {
        return $this->hasMany(State::class);
    }
}
