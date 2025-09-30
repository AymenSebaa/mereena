<?php

namespace Modules\Saas\Http\Controllers;

use App\Http\Controllers\BaseCrudController;
use Modules\Saas\Models\Plan;

class PlanController extends BaseCrudController {
    protected string $modelClass = Plan::class;
    protected string $viewPrefix = 'saas::plans';

    protected function rules(): array {
        return [
            'name'     => 'required|string|max:255',
            'slug'     => 'nullable|string|max:255|unique:plans,slug,' . request()->id,
            'price'    => 'required|numeric|min:0',
            'interval' => 'required|string|max:50',
            'features' => 'nullable|json',
        ];
    }

    protected function label(): string {
        return 'Plan';
    }
}
