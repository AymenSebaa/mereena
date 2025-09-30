<?php

use App\Http\Middleware\EnsureOtpVerified;
use Illuminate\Support\Facades\Route;
use Modules\World\Http\Controllers\InstallController;
use Modules\World\Http\Controllers\ContinentController;
use Modules\World\Http\Controllers\RegionController;
use Modules\World\Http\Controllers\CountryController;
use Modules\World\Http\Controllers\StateController;
use Modules\World\Http\Controllers\CityController;

Route::middleware(['web', 'auth', EnsureOtpVerified::class])
    ->prefix('{organization_slug?}')
    ->group(function () {

        Route::prefix('world')->group(function () {

            // CRUD routes using helper
            udsRoutes('continents', ContinentController::class, 'world.continents');
            udsRoutes('regions', RegionController::class, 'world.regions');
            udsRoutes('countries', CountryController::class, 'world.countries');
            udsRoutes('states', StateController::class, 'world.states');
            udsRoutes('cities', CityController::class, 'world.cities');

            // Install route
            Route::get('/install', [InstallController::class, 'install'])->name('world.install');
        });
    });
