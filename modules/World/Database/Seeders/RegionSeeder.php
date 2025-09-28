<?php

namespace Modules\World\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\World\Models\Region;
use Modules\World\Models\Continent;

class RegionSeeder extends Seeder {
    public function run(): void {
        $regions = [
            // Africa (002)
            ['name' => 'Northern Africa', 'm49_code' => 15, 'continent' => 'Africa'],
            ['name' => 'Western Africa',  'm49_code' => 11, 'continent' => 'Africa'],
            ['name' => 'Eastern Africa',  'm49_code' => 14, 'continent' => 'Africa'],
            ['name' => 'Middle Africa',   'm49_code' => 17, 'continent' => 'Africa'],
            ['name' => 'Southern Africa', 'm49_code' => 18, 'continent' => 'Africa'],

            // Americas (019)
            ['name' => 'Northern America', 'm49_code' => 21, 'continent' => 'Americas'],
            ['name' => 'Central America',  'm49_code' => 13, 'continent' => 'Americas'],
            ['name' => 'Caribbean',        'm49_code' => 29, 'continent' => 'Americas'],
            ['name' => 'South America',    'm49_code' => 5,  'continent' => 'Americas'],

            // Asia (142)
            ['name' => 'Central Asia',      'm49_code' => 143, 'continent' => 'Asia'],
            ['name' => 'Eastern Asia',      'm49_code' => 30,  'continent' => 'Asia'],
            ['name' => 'Southern Asia',     'm49_code' => 34,  'continent' => 'Asia'],
            ['name' => 'South-Eastern Asia', 'm49_code' => 35,  'continent' => 'Asia'],
            ['name' => 'Western Asia',      'm49_code' => 145, 'continent' => 'Asia'],

            // Europe (150)
            ['name' => 'Eastern Europe', 'm49_code' => 151, 'continent' => 'Europe'],
            ['name' => 'Northern Europe', 'm49_code' => 154, 'continent' => 'Europe'],
            ['name' => 'Southern Europe', 'm49_code' => 39,  'continent' => 'Europe'],
            ['name' => 'Western Europe', 'm49_code' => 155, 'continent' => 'Europe'],

            // Oceania (009)
            ['name' => 'Australia and New Zealand', 'm49_code' => 53, 'continent' => 'Oceania'],
            ['name' => 'Melanesia',                 'm49_code' => 54, 'continent' => 'Oceania'],
            ['name' => 'Micronesia',                'm49_code' => 57, 'continent' => 'Oceania'],
            ['name' => 'Polynesia',                 'm49_code' => 61, 'continent' => 'Oceania'],
        ];

        foreach ($regions as $r) {
            $continent = Continent::where('name', $r['continent'])->first();
            if ($continent) {
                Region::firstOrCreate(
                    ['m49_code' => $r['m49_code']],
                    ['name' => $r['name'], 'continent_id' => $continent->id]
                );
            }
        }
    }
}
