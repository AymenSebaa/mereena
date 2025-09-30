<?php

namespace Modules\Saas\Http\Controllers;

use App\Http\Controllers\BaseCrudController;
use Modules\Saas\Models\Subscription;

class SubscriptionController extends BaseCrudController {
    protected string $modelClass = Subscription::class;
    protected string $viewPrefix = 'saas::subscriptions';
    protected array $with = ['organization', 'plan'];


    protected function rules(): array {
        return [
            'organization_id' => 'required|exists:organizations,id',
            'plan_id'         => 'required|exists:plans,id',
            'status'          => 'required|string|max:50',
            'starts_at'       => 'nullable|date',
            'ends_at'         => 'nullable|date',
        ];
    }

    protected function label(): string {
        return 'Subscription';
    }

    protected function with(): array {
        return ['organization', 'plan', 'invoices'];
    }
}
