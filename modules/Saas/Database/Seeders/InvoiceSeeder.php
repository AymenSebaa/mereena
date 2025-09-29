<?php

namespace Modules\Saas\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Saas\Models\Invoice;
use Modules\Saas\Models\Organization;
use Modules\Saas\Models\Subscription;
use Illuminate\Support\Str;
use Carbon\Carbon;

class InvoiceSeeder extends Seeder {
    public function run(): void {
        $organization = Organization::first();
        $subscription = Subscription::first();

        if ($organization && $subscription) {
            Invoice::create([
                'organization_id' => $organization->id,
                'subscription_id' => $subscription->id,
                'invoice_number' => strtoupper(Str::random(8)),
                'amount' => 29.99,
                'currency' => 'USD',
                'status' => 'unpaid',
                'issued_at' => Carbon::now(),
                'due_at' => Carbon::now()->addDays(7),
            ]);
        }
    }
}
