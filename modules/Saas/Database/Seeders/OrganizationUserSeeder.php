<?php

namespace Modules\Saas\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Saas\Models\OrganizationUser;
use Modules\Saas\Models\Organization;
use App\Models\User;

class OrganizationUserSeeder extends Seeder {
    public function run(): void {
        $organization = Organization::first();
        $user = User::first(); // assume at least one user exists

        if ($organization && $user) {
            OrganizationUser::create([
                'organization_id' => $organization->id,
                'user_id' => $user->id,
                'role' => 'owner',
            ]);
        }
    }
}
