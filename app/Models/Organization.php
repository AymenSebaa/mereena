<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Organization extends Model {
    use SoftDeletes;
    
    protected $fillable = [
        'name',
        'slug',
        'email',
        'phone',
        'address',
        'settings',
    ];

    protected $casts = [
        'settings' => 'array',
    ];


}
