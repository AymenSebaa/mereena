<?php

namespace Modules\World\Models;

use Illuminate\Database\Eloquent\Model;

class State extends Model {

    protected $fillable = [
        'country_id',
        'name',
        'iso2',
        'lat',
        'lng',
    ];

    public function country() {
        return $this->belongsTo(Country::class);
    }

    public function cities() {
        return $this->hasMany(City::class);
    }
}
