<?php

namespace Modules\Saas\Models;

use App\Models\Organization as BaseOrganization;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\SoftDeletes;

class Organization extends BaseOrganization {
    use SoftDeletes;

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
