<?php

namespace Modules\Sale;

use Illuminate\Support\ServiceProvider;

class SaleServiceProvider extends ServiceProvider {
    public function boot() {
        // Load routes
        $this->loadRoutesFrom(__DIR__ . '/Routes/web.php');
        $this->loadRoutesFrom(__DIR__ . '/Routes/api.php');

        // Load migrations
        $this->loadMigrationsFrom(__DIR__ . '/Database/Migrations');

        // Load views
        $this->loadViewsFrom(__DIR__ . '/Views', 'sale');

        // Publish config
        $this->mergeConfigFrom(__DIR__ . '/Config/module.php', 'modules.sale');
    }

    public function register() {
        //
    }
}
