<?php

namespace Modules\Saas\Http\Controllers;

use App\Http\Controllers\BaseCrudController;
use Modules\Saas\Models\Organization;

class OrganizationController extends BaseCrudController {
    protected string $modelClass = Organization::class;
    protected string $viewPrefix = 'saas::organizations';
    protected array $searchable = ['name', 'slug', 'email', 'phone', 'address'];
    protected array $orderBy = ['name' => 'asc'];

    protected function rules(): array {
        $id = request()->id; // grab ID for unique validation
        return [
            'name'    => 'required|string|max:255',
            'slug'    => 'nullable|string|max:255|unique:organizations,slug,' . $id,
            'email'   => 'nullable|email|max:255',
            'phone'   => 'nullable|string|max:50',
            'address' => 'nullable|string|max:500',
        ];
    }

    protected function label(): string {
        return 'Organization';
    }
}
