<?php

namespace Modules\Saas\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrganizationUser extends Model {
    protected $fillable = [
        'organization_id',
        'user_id',
        'role',
    ];

    // Relations
    public function organization(): BelongsTo {
        return $this->belongsTo(Organization::class);
    }

    public function user(): BelongsTo {
        return $this->belongsTo(User::class);
    }
}
