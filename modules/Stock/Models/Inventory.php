<?php

namespace Modules\Stock\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\BelongsToOrganization;

class Inventory extends Model {
    use SoftDeletes, BelongsToOrganization;

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

}
