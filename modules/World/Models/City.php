<?php

namespace Modules\World\Models;

use Illuminate\Database\Eloquent\Model;

class City extends Model {

    protected $fillable = [
        'state_id',
        'name',
        'zip_code',
        'lat',
        'lng',
    ];

    public function state() {
        return $this->belongsTo(State::class);
    }

}
