<?php

namespace Modules\World;

use Illuminate\Support\ServiceProvider;

class WorldServiceProvider extends ServiceProvider {

    public function boot() {
        // Load routes
        $this->loadRoutesFrom(__DIR__ . '/Routes/web.php');
        $this->loadRoutesFrom(__DIR__ . '/Routes/api.php');

        // Load migrations
        $this->loadMigrationsFrom(__DIR__ . '/Database/Migrations');


        // Load views
        $this->loadViewsFrom(__DIR__ . '/Views', 'world');

        // Publish config
        $this->mergeConfigFrom(__DIR__ . '/Config/module.php', 'modules.world');
    }

    public function register() {
        //
    }
}
