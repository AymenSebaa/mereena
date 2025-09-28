<?php

namespace Modules\Stock\Models;

use App\Models\Company;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model {
    use SoftDeletes;

    protected $fillable = [
        'company_id',
        'name',
        'sku',
        'description',
        'category',
        'brand',
        'images',
        'price'
    ];

    protected $casts = [
        'images' => 'array',
    ];

    public function company() {
        return $this->belongsTo(Company::class);
    }

    public function inventories() {
        return $this->hasMany(Inventory::class);
    }

    public function currentInventory() {
        return $this->inventories()->sum('quantity');
    }
}
