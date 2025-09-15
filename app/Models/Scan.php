<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Scan extends Model {
    use HasFactory;

    protected $fillable = [
        'user_id',
        'type',
        'type_id',
        'content',
        'extra',
        'lat',
        'lng',
    ];

    protected $casts = [
        'content' => 'json',
    ];

    public function scanable() {
        return $this->morphTo(__FUNCTION__, 'type', 'type_id');
    }

    public function user() {
        return $this->belongsTo(User::class);
    }

    public function bus() {
        return $this->belongsTo(Bus::class, 'type_id');
    }

    public function hotel() {
        return $this->belongsTo(Hotel::class, 'type_id');
    }

    public function guest() {
        return $this->belongsTo(User::class, 'type_id');
    }
}
