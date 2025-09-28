<?php

namespace Modules\Example\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Item extends Model {
    use SoftDeletes;

    protected $fillable = [
        'type_id',
        'name',
        'price',
        'desc',
        'images',
        'expires_at',
    ];

    protected $dates = ['expires_at'];

    protected $casts = [
        'images' => 'array',
    ];
}
