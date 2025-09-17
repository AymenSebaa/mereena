<?php

namespace Modules\Stock\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Inventory extends Model {
    use SoftDeletes;

    protected $fillable = [
        'product_id',
        'supplier_id',
        'made_at',
        'expires_at',
        'quantity',
        'cost_price',
        'batch_no'
    ];

    protected $dates = ['made_at', 'expires_at'];

    public function product() {
        return $this->belongsTo(Product::class);
    }

    public function supplier() {
        return $this->belongsTo(User::class);
    }
}
