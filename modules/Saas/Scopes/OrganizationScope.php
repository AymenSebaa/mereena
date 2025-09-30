<?php

namespace Modules\Saas\Scopes;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;
use Illuminate\Support\Facades\Auth;
use Modules\Saas\Models\OrganizationUser;

class OrganizationScope implements Scope {
    public function apply(Builder $builder, Model $model) {
        if (!class_exists(OrganizationUser::class)) return;

        $orgId = Auth::user()?->organizationUser?->organization_id;
        if (!$orgId) return;

        $builder->where($model->getTable() . '.organization_id', $orgId);
    }
}
