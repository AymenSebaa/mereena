<?php

namespace Modules\World\Http\Controllers;

use Illuminate\Routing\Controller;

class InstallController extends Controller {
    public function install() {
        $results = [];

        $results['continents'] = installModule('World', 'continents', 'ContinentSeeder');
        $results['regions'] = installModule('World', 'regions', 'RegionsSeeder');
        $results['countries'] = installModule('World', 'countries', 'CountrySeeder');
        $results['states'] = installModule('World', 'states', 'StateSeeder');
        $results['cities'] = installModule('World', 'cities', 'CitySeeder');

        return response()->json($results);
    }
}
