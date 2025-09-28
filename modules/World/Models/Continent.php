<?php

namespace Modules\World\Models;

use Illuminate\Database\Eloquent\Model;

class Continent extends Model {

    protected $fillable = [
        'name',
        'm49_code'
    ];

    public function regions() {
        return $this->hasMany(Region::class);
    }
}
