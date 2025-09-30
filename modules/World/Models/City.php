<?php

namespace Modules\World\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class City extends Model {
    use SoftDeletes;
    
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
