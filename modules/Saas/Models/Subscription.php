<?php

namespace Modules\Saas\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Subscription extends Model {
    use SoftDeletes;

    protected $fillable = [
        'organization_id',
        'plan_id',
        'starts_at',
        'ends_at',
        'status',
    ];

    protected $casts = [
        'starts_at' => 'datetime',
        'ends_at' => 'datetime',
    ];

    // Relations
    public function organization(): BelongsTo {
        return $this->belongsTo(Organization::class);
    }

    public function plan(): BelongsTo {
        return $this->belongsTo(Plan::class);
    }

    public function invoices(): HasMany {
        return $this->hasMany(Invoice::class);
    }
}
