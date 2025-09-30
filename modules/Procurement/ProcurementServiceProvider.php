<?php

namespace Modules\Procurement;

use Illuminate\Support\ServiceProvider;
use Modules\Procurement\Models\Supplier;
use Modules\Stock\Models\Inventory;
use Warehouse;

class ProcurementServiceProvider extends ServiceProvider {
    public function boot() {
        // Load routes
        $this->loadRoutesFrom(__DIR__ . '/Routes/web.php');
        $this->loadRoutesFrom(__DIR__ . '/Routes/api.php');

        // Load migrations
        $this->loadMigrationsFrom(__DIR__ . '/Database/Migrations');

        // Load views
        $this->loadViewsFrom(__DIR__ . '/Views', 'procurement');

        // Publish config
        $this->mergeConfigFrom(__DIR__ . '/Config/module.php', 'modules.procurement');

        $this->registerRelations();
    }

    private function registerRelations() {    
        Inventory::resolveRelationUsing('supplier', function ($inventoryModel) {
            return $inventoryModel->belongsTo(Supplier::class);
        });

        Inventory::resolveRelationUsing('warehouse', function ($inventoryModel) {
            return $inventoryModel->belongsTo(Warehouse::class);
        });
    }

    public function register() {
        //
    }

    
}
