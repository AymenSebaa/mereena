<?php

namespace Modules\Saas\Http\Controllers;

use Illuminate\Routing\Controller;

class InstallController extends Controller {
    public function install() {
        $results = [];

        $results['organizations'] = installModule('Saas', 'organizations', 'OrganizationSeeder');
        $results['organization_users'] = installModule('Saas', 'organization_users', 'OrganizationUserSeeder');
        $results['plans'] = installModule('Saas', 'plans', 'PlanSeeder');
        $results['subscriptions'] = installModule('Saas', 'subscriptions', 'SubscriptionSeeder');
        $results['invoices'] = installModule('Saas', 'invoices', 'InvoiceSeeder');

        return response()->json($results);
    }
}
