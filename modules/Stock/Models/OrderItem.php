<?php

namespace Modules\Stock\Models;

use Illuminate\Database\Eloquent\Model;

class OrderItem extends Model {
    protected $fillable = [
        'order_id',
        'product_id',
        'inventory_id',
        'supplier_id',
        'quantity',
        'unit_price',
        'subtotal'
    ];

    public function order() {
        return $this->belongsTo(Order::class);
    }

    public function product() {
        return $this->belongsTo(Product::class);
    }

    public function inventory() {
        return $this->belongsTo(Inventory::class);
    }

    public function supplier() {
        return $this->belongsTo(Supplier::class);
    }
}
