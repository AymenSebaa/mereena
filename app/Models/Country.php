<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Country extends Model {
    // Explicitly set the table (since default would be "countries")
    protected $table = 'countries';

    // Primary key
    protected $primaryKey = 'id';

    // Laravel will auto-manage created_at & updated_at since they exist in your table
    public $timestamps = true;

    // Mass assignable fields
    protected $fillable = [
        'name_en',
        'name_fr',
        'name_ar',
        'iso2',
        'iso3',
        'phone_code',
        'flag',
        'latitude',
        'longitude',
    ];
}
