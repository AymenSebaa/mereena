<?php

namespace Modules\Saas\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

class Organization extends Model {
    protected $fillable = [
        'name',
        'slug',
        'email',
        'phone',
        'address',
        'settings',
    ];

    protected $casts = [
        'settings' => 'array',
    ];

    // Relations
    public function users(): HasMany {
        return $this->hasMany(OrganizationUser::class);
    }

    public function subscriptions(): HasMany {
        return $this->hasMany(Subscription::class);
    }

    public function invoices(): HasMany {
        return $this->hasMany(Invoice::class);
    }

    public function plans(): HasManyThrough {
        return $this->hasManyThrough(Plan::class, Subscription::class);
    }
}
