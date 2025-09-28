<?php

namespace Modules\Example;

use Illuminate\Support\ServiceProvider;

class ExampleServiceProvider extends ServiceProvider {
    public function boot() {
        // Load routes
        $this->loadRoutesFrom(__DIR__ . '/Routes/web.php');
        $this->loadRoutesFrom(__DIR__ . '/Routes/api.php');

        // Load migrations
        $this->loadMigrationsFrom(__DIR__ . '/Database/Migrations');

        // Load views
        $this->loadViewsFrom(__DIR__ . '/Views', 'example');

        // Publish config
        $this->mergeConfigFrom(__DIR__ . '/Config/module.php', 'modules.example');
    }

    public function register() {
        //
    }
}
