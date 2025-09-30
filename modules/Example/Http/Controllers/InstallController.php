<?php

namespace Modules\Example\Http\Controllers;

use Illuminate\Routing\Controller;

class InstallController extends Controller {
    public function install() {
        $results = [];

        $results['items'] = installModule('Saas', 'items', 'ItemSeeder');

        return response()->json($results);
    }
}
