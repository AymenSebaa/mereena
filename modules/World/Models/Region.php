<?php

namespace Modules\World\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Region extends Model {
    use SoftDeletes;

    protected $fillable = [
        'continent_id',
        'name', 
        'm49_code'
    ];

    public function continent() {
        return $this->belongsTo(Continent::class);
    }

    public function countries() {
        return $this->hasMany(Country::class);
    }
}
