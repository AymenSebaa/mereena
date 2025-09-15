<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HotelZone extends Model {
    protected $table = 'hotel_zone';

    protected $fillable = [
        'hotel_id',
        'zone_id',
    ];

    public function hotel() {
        return $this->belongsTo(Hotel::class);
    }

    public function zone() {
        return $this->belongsTo(Zone::class);
    }
}
