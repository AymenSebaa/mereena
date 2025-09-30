<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Type extends Model {
    use SoftDeletes, HasFactory;

    protected $fillable = [
        'type_id',
        'status',
        'name',
    ];

    public function subTypes() {
        return $this->hasMany(Type::class);
    }

    public function parent() {
        return $this->belongsTo(Type::class);
    }
}
