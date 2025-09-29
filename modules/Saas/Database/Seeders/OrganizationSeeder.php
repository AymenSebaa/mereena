<?php

namespace Modules\Saas\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Saas\Models\Organization;

class OrganizationSeeder extends Seeder {
    public function run(): void {
        Organization::create([
            'name' => 'Demo Company',
            'slug' => 'demo-company',
            'email' => 'contact@demo.com',
            'phone' => '+213123456789',
            'address' => 'Algiers, Algeria',
            'settings' => [
                'timezone' => 'Africa/Algiers',
                'locale' => 'fr',
            ],
        ]);
    }
}
