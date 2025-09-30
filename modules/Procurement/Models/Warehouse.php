<?php

use App\Models\Site;
use App\Models\Organization;
use Modules\Stock\Models\Inventory;

class Warehouse extends Site {
    public function inventory() {
        return $this->hasMany(Inventory::class);
    }

    public function organization() {
        return $this->belongsTo(Organization::class);
    }
}
