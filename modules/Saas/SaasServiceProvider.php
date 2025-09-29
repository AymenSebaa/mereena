<?php

namespace Modules\Saas;

use Illuminate\Support\ServiceProvider;

class SaasServiceProvider extends ServiceProvider {
    public function boot() {
        // Load routes
        $this->loadRoutesFrom(__DIR__ . '/Routes/web.php');
        $this->loadRoutesFrom(__DIR__ . '/Routes/api.php');

        // Load migrations
        $this->loadMigrationsFrom(__DIR__ . '/Database/Migrations');

        // Load views
        $this->loadViewsFrom(__DIR__ . '/Views', 'saas');

        // Publish config
        $this->mergeConfigFrom(__DIR__ . '/Config/module.php', 'modules.saas');
    }

    public function register() {
        //
    }
}
