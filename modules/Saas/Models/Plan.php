<?php

namespace Modules\Saas\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Plan extends Model {
    use SoftDeletes;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'price',
        'currency',
        'interval',
        'features',
    ];

    protected $casts = [
        'features' => 'array',
    ];

    // Relations
    public function subscriptions(): HasMany {
        return $this->hasMany(Subscription::class);
    }
}
