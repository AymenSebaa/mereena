<?php

namespace Modules\Saas\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Saas\Models\Plan;

class PlanSeeder extends Seeder {
    public function run(): void {
        Plan::insert([
            [
                'name' => 'Free',
                'slug' => 'free',
                'description' => 'Basic free plan with limited features.',
                'price' => 0,
                'currency' => 'DZD',
                'interval' => 'monthly',
                'features' => json_encode(['1 user', 'Basic support']),
            ],
            [
                'name' => 'Pro',
                'slug' => 'pro',
                'description' => 'Professional plan for small teams.',
                'price' => 3000,
                'currency' => 'DZD',
                'interval' => 'monthly',
                'features' => json_encode(['5 users', 'Priority support', 'Analytics']),
            ],
            [
                'name' => 'Enterprise',
                'slug' => 'enterprise',
                'description' => 'Enterprise-grade features for large orgs.',
                'price' => 10000,
                'currency' => 'DZD',
                'interval' => 'monthly',
                'features' => json_encode(['Unlimited users', 'Dedicated support', 'Advanced reports']),
            ],
        ]);
    }
}
