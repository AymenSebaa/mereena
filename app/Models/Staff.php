<?php

namespace App\Models;

use App\Models\User;
use App\Models\Organization;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\BelongsToOrganization;

class Staff extends User {
    use SoftDeletes, BelongsToOrganization;

    protected $table = 'users';

    public function organization() {
        return $this->belongsTo(Organization::class, 'organization_id');
    }
}
