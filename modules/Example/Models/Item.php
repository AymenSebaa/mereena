<?php

namespace Modules\Example\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\BelongsToOrganization;
class Item extends Model {
    use SoftDeletes, BelongsToOrganization;

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
