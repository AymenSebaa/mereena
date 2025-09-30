<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Profile extends Model {
    use HasFactory;

    protected $fillable = [
        'user_id', 
        'role_id', 
        'category',
        'address',
        'country_id', 
        'site_id',
        'zone_id',
        'phone',
        'lat',
        'lng',
    ];

    public function user() {
        return $this->belongsTo(User::class);
    }

    public function role() {
        return $this->belongsTo(Role::class);
    }

    public function country() {
        return $this->belongsTo(Country::class);
    }

    public function site() {
        return $this->belongsTo(Hotel::class);
    }

    public function zone() {
        return $this->belongsTo(Zone::class);
    }

    public function category() {
        return $this->belongsTo(Type::class, 'category', 'name');
    }
}
