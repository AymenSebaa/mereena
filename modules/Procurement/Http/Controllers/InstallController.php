<?php

namespace Modules\Procurement\Http\Controllers;

use Illuminate\Routing\Controller;

class InstallController extends Controller {
    public function install() {
        $results = [];

        $results['suppliers'] = installModule('Procurement', 'suppliers', 'SupplierSeeder');

        return response()->json($results);
    }
}
