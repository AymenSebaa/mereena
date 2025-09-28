<?php

namespace Modules\World\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\World\Models\Continent;

class ContinentSeeder extends Seeder {
    public function run(): void {
        $continents = [
            ['name' => 'Africa',        'm49_code' => 2],
            ['name' => 'Americas',      'm49_code' => 19],
            ['name' => 'Asia',          'm49_code' => 142],
            ['name' => 'Europe',        'm49_code' => 150],
            ['name' => 'Oceania',       'm49_code' => 9],
            ['name' => 'Antarctica',    'm49_code' => 10],
            ['name' => 'World',         'm49_code' => 1], // UN global code
        ];

        foreach ($continents as $c) {
            Continent::firstOrCreate(
                ['m49_code' => $c['m49_code']],
                ['name' => $c['name']]
            );
        }
    }
}
