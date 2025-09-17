<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\File;

class AppServiceProvider extends ServiceProvider {
    /**
     * Register any application services.
     */
    public function register(): void {
        $this->registerModules();
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void {
        Paginator::useBootstrapFive();
        require_once app_path('Helpers/helpers.php');
    }

    /**
     * Auto-register all module service providers.
     */
    protected function registerModules(): void {
        $modulesPath = base_path('modules');

        if (!File::exists($modulesPath)) {
            return;
        }

        foreach (File::directories($modulesPath) as $moduleDir) {
            $moduleName = basename($moduleDir);
            $provider   = $moduleDir . '/' . $moduleName . 'ServiceProvider.php';

            if (File::exists($provider)) {
                $class = "Modules\\{$moduleName}\\{$moduleName}ServiceProvider";
                // $this->app->register($class);
            }
        }
    }
}
