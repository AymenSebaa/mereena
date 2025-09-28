<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SiteZone extends Model {
    protected $table = 'site_zone';

    protected $fillable = [
        'site_id',
        'zone_id',
    ];

    public function site() {
        return $this->belongsTo(Site::class);
    }

    public function zone() {
        return $this->belongsTo(Zone::class);
    }
}
