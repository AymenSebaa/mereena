<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Bus extends Model {
    use HasFactory;

    protected $fillable = [
        'external_id',
        'name',
        'lat',
        'lng',
        'status',
        'type_id',
        'company_id',
    ];

    public function type() {
        return $this->belongsTo(Type::class, 'type_id');
    }

    public function tasks() {
        return $this->hasMany(Task::class, 'device_id', 'external_id');
    }

    public function scans() {
        return $this->hasMany(Scan::class, 'type_id', 'id')
            ->where('type', 'buses')
            ->with('user'); // eager load user
    }

    public function company() {
        return $this->belongsTo(Company::class);
    }
}
