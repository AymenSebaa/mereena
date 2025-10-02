<?php

namespace Modules\Stock\Models;

use App\Models\Company;
use App\Models\Type;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\BelongsToOrganization;

class Product extends Model {
    use SoftDeletes, BelongsToOrganization;

    protected $fillable = [
        'organization_id',
        'company_id',
        'category_id',
        'name',
        'sku',
        'description',
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

    public function category() {
        return $this->belongsTo(Type::class, 'category_id');
    }

    public function inventories() {
        return $this->hasMany(Inventory::class);
    }

    public function currentInventory() {
        return $this->inventories()->sum('quantity');
    }
}
