<?php

namespace Modules\Procurement\Models;

use App\Models\User;
use App\Models\Organization;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\BelongsToOrganization;

class Supplier extends User {
    use SoftDeletes, BelongsToOrganization;

    protected $table = 'users';

    protected static function booted() {
        static::addGlobalScope('supplier', function ($query) {
            $query->whereHas('profile.role', function ($q) {
                $q->where('name', 'Supplier');
            });
        });
    }

    public function organization() {
        return $this->belongsTo(Organization::class, 'organization_id');
    }
}
