<?php

namespace Modules\World\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\World\Models\Country;
use Modules\World\Models\Region;

class CountrySeeder extends Seeder {
    public function run(): void {
        $countries = [
            [
                'name'      => 'Algeria',
                'iso2'      => 'DZ',
                'iso3'      => 'DZA',
                'phone_code'=> '+213',
                'currency'  => 'DZD',
                'emoji'     => '🇩🇿',
                'lat'       => 28.0339,
                'lng'       => 1.6596,
                'region'    => 'Northern Africa',
            ],
            // Add more countries here if needed
        ];

        foreach ($countries as $c) {
            $region = Region::where('name', $c['region'])->first();
            if ($region) {
                Country::firstOrCreate(
                    ['iso2' => $c['iso2']],
                    [
                        'name'       => $c['name'],
                        'region_id'  => $region->id,
                        'iso3'      => $c['iso3'],
                        'phone_code'=> $c['phone_code'],
                        'currency'  => $c['currency'],
                        'emoji'     => $c['emoji'],
                        'lat'       => $c['lat'],
                        'lng'       => $c['lng'],
                    ]
                );
            }
        }
    }
}
