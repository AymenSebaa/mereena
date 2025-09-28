<?php

namespace Modules\Stock\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Order extends Model {
    use SoftDeletes;

    protected $fillable = [
        'company_id',
        'status_id',
        'customer_name',
        'customer_email',
        'customer_phone',
        'billing_address',
        'total'
    ];

    public function items() {
        return $this->hasMany(OrderItem::class);
    }

    public function status() {
        return $this->belongsTo(\App\Models\Type::class, 'status_id');
    }

    public function company() {
        return $this->belongsTo(\App\Models\Company::class);
    }
}
