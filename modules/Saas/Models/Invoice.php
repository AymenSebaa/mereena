<?php

namespace Modules\Saas\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;


class Invoice extends Model {
    use SoftDeletes;

    protected $fillable = [
        'organization_id',
        'subscription_id',
        'invoice_number',
        'amount',
        'currency',
        'status',
        'issued_at',
        'due_at',
        'paid_at',
    ];

    protected $casts = [
        'issued_at' => 'datetime',
        'due_at' => 'datetime',
        'paid_at' => 'datetime',
    ];

    // Relations
    public function organization(): BelongsTo {
        return $this->belongsTo(Organization::class);
    }

    public function subscription(): BelongsTo {
        return $this->belongsTo(Subscription::class);
    }
}
