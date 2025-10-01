<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Modules\World\Models\State;
use Modules\World\Models\Country;

class Profile extends Model {
    use HasFactory;

    protected $fillable = [
        'user_id', 
        'role_id', 
        'category',
        'address',
        'country_id', 
        'state_id', 
        'site_id',
        'zone_id',
        'phone',
        'sector',
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

    public function state() {
        return $this->belongsTo(State::class);
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
