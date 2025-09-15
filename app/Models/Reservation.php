<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Reservation extends Model {
    use HasFactory;

    protected $fillable = [
        'user_id',
        'type_id',
        'status_id',
        'pickup_time',
        'content',
        'edited_by',
        'edited_at',
        'note',
    ];

    protected $casts = [
        'pickup_time' => 'datetime',
        'content' => 'json',
        'edited_at' => 'datetime',
    ];

    // Relations
    public function user() {
        return $this->belongsTo(User::class);
    }

    public function type() {
        return $this->belongsTo(Type::class);
    }

    public function status() {
        return $this->belongsTo(Type::class, 'status_id');
    }

    public function editor() {
        return $this->belongsTo(User::class, 'edited_by');
    }
}
