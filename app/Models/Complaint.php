<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Complaint extends Model {
    protected $fillable = [
        'type_id',
        'user_id',
        'status_id',
        'subject',
        'body',
        'decided_by',
        'decided_at',
    ];

    protected $casts = [
        'decided_at' => 'datetime',
    ];

    /**
     * The type of the complaint
     */
    public function type() {
        return $this->belongsTo(Type::class);
    }

    /**
     * The status of the complaint
     */
    public function status() {
        return $this->belongsTo(Type::class, 'status_id');
    }

    /**
     * The user who submitted the complaint
     */
    public function user() {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * The admin/decider who reviewed the complaint
     */
    public function decider() {
        return $this->belongsTo(User::class, 'decided_by');
    }
}
