<?php

namespace Modules\Saas\Traits;

use Modules\Saas\Scopes\OrganizationScope;
use Illuminate\Support\Facades\Auth;
use Modules\Saas\Models\Organization;

trait BelongsToOrganization {
    protected static function bootBelongsToOrganization() {
        // Apply global scope only if SaaS exists
        if (class_exists(OrganizationScope::class)) {
            static::addGlobalScope(new OrganizationScope);
        }

        // Auto-fill organization_id on create/update
        static::creating(function ($model) {
            if (!$model->organization_id && $orgId = Auth::user()?->organizationUser?->organization_id) {
                $model->organization_id = $orgId;
            }
        });
        static::updating(function ($model) {
            if ($orgId = Auth::user()?->organizationUser?->organization_id) {
                $model->organization_id = $orgId;
            }
        });
    }

    public function organization() {
        return $this->belongsTo(Organization::class);
    }
}
