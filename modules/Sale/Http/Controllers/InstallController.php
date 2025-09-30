<?php

namespace Modules\Sale\Http\Controllers;

use Illuminate\Routing\Controller;

class InstallController extends Controller {
    public function install() {
        $results = [];

        $results['orders'] = installModule('Sale', 'orders', 'OrderSeeder');

        return response()->json($results);
    }
}
