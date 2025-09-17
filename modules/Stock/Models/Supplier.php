<?php

namespace Modules\Product\Entities;

use App\Models\Company;
use Illuminate\Database\Eloquent\Model;
use Modules\Stock\Models\Inventory;

class Supplier extends Model {
    protected $fillable = [
        'company_id',
        'name',
        'email',
        'phone',
        'address',
        'contact_name',
    ];

    public function company() {
        return $this->belongsTo(Company::class);
    }

    public function inventories() {
        return $this->hasMany(Inventory::class);
    }
}
