<?php

namespace Modules\World\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Continent extends Model {
    use SoftDeletes;

    protected $fillable = [
        'name',
        'm49_code'
    ];

    public function regions() {
        return $this->hasMany(Region::class);
    }
}
