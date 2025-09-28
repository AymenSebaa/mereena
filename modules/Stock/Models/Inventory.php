<?php

namespace Modules\Stock\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Inventory extends Model {
    use SoftDeletes;

    protected $fillable = [
        'product_id',
        'supplier_id',
        'price',
        'quantity',
        'made_at',
        'expires_at',
        'cost_price',
        'batch_no'
    ];

    protected $dates = ['made_at', 'expires_at'];

    public function product() {
        return $this->belongsTo(Product::class);
    }

    public function supplier() {
        return $this->belongsTo(Supplier::class);
    }
}
