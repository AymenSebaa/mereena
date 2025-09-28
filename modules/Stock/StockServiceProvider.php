<?php

namespace Modules\Stock;

use Illuminate\Support\ServiceProvider;

class StockServiceProvider extends ServiceProvider {
    public function boot() {
        // Load routes
        $this->loadRoutesFrom(__DIR__ . '/Routes/web.php');
        $this->loadRoutesFrom(__DIR__ . '/Routes/api.php');

        // Load migrations
        $this->loadMigrationsFrom(__DIR__ . '/Database/Migrations');

        // Load views
        $this->loadViewsFrom(__DIR__ . '/Views', 'stock');

        // Publish config
        $this->mergeConfigFrom(__DIR__ . '/Config/module.php', 'modules.stock');
    }

    public function register() {
        //
    }
}
