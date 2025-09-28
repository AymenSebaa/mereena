<?php

namespace Modules\Stock\Models;

use App\Models\Company;
use Illuminate\Database\Eloquent\Model;
use Modules\Stock\Models\Inventory;

class Supplier extends Model {
    protected $fillable = [
        'name',
        'contact',
        'email',
        'phone',
        'address',
    ];

    public function company() {
        return $this->belongsTo(Company::class);
    }

    public function inventories() {
        return $this->hasMany(Inventory::class);
    }
}
