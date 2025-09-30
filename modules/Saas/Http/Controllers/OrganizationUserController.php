<?php

namespace Modules\Saas\Http\Controllers;

use App\Http\Controllers\BaseCrudController;
use Modules\Saas\Models\OrganizationUser;

class OrganizationUserController extends BaseCrudController {
    protected string $modelClass = OrganizationUser::class;
    protected string $viewPrefix = 'saas::organization_users';
    protected array $with = ['organization', 'user'];

    protected function rules(): array {
        return [
            'organization_id' => 'required|exists:organizations,id',
            'user_id'         => 'required|exists:users,id',
            'role'            => 'required|string|max:50',
        ];
    }

    protected function label(): string {
        return 'Organization User';
    }
}
