<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PushSubscription extends Model {
    protected $fillable = ['user_id', 'push_ids'];

    protected $casts = [
        'push_ids' => 'array',
    ];

    public function user() {
        return $this->belongsTo(User::class);
    }
}
