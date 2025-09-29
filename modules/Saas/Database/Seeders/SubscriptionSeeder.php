<?php

namespace Modules\Saas\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Saas\Models\Subscription;
use Modules\Saas\Models\Organization;
use Modules\Saas\Models\Plan;
use Carbon\Carbon;

class SubscriptionSeeder extends Seeder {
    public function run(): void {
        $organization = Organization::first();
        $plan = Plan::where('slug', 'pro')->first();

        if ($organization && $plan) {
            Subscription::create([
                'organization_id' => $organization->id,
                'plan_id' => $plan->id,
                'starts_at' => Carbon::now(),
                'ends_at' => Carbon::now()->addMonth(),
                'status' => 'active',
            ]);
        }
    }
}
