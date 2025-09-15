<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Type extends Model {
    use HasFactory;

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
