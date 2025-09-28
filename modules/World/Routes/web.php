<?php

use Illuminate\Support\Facades\Route;
use Modules\World\Http\Controllers\ContinentController;
use Modules\World\Http\Controllers\RegionController;
use Modules\World\Http\Controllers\CountryController;
use Modules\World\Http\Controllers\StateController;
use Modules\World\Http\Controllers\CityController;

Route::prefix('world')->middleware(['web', 'auth'])->group(function () {
    Route::prefix('continents')->group(function () {
        Route::get('/', [ContinentController::class, 'index'])->name('world.continents.index');
        Route::post('/upsert', [ContinentController::class, 'upsert'])->name('world.continents.upsert');
        Route::delete('/{id}', [ContinentController::class, 'delete'])->name('world.continents.delete');
        Route::get('/install', [ContinentController::class, 'install'])->name('world.continents.install');
    });

    Route::prefix('regions')->group(function () {
        Route::get('/', [RegionController::class, 'index'])->name('world.regions.index');
        Route::post('/upsert', [RegionController::class, 'upsert'])->name('world.regions.upsert');
        Route::delete('/{id}', [RegionController::class, 'delete'])->name('world.regions.delete');
        Route::get('/install', [RegionController::class, 'install'])->name('world.regions.install');
    });

    Route::prefix('countries')->group(function () {
        Route::get('/', [CountryController::class, 'index'])->name('world.countries.index');
        Route::post('/upsert', [CountryController::class, 'upsert'])->name('world.countries.upsert');
        Route::delete('/{id}', [CountryController::class, 'delete'])->name('world.countries.delete');
        Route::get('/install', [CountryController::class, 'install'])->name('world.countries.install');
    });

    Route::prefix('states')->group(function () {
        Route::get('/', [StateController::class, 'index'])->name('world.states.index');
        Route::post('/upsert', [StateController::class, 'upsert'])->name('world.states.upsert');
        Route::delete('/{id}', [StateController::class, 'delete'])->name('world.states.delete');
        Route::get('/install', [StateController::class, 'install'])->name('world.states.install');
        Route::post('/upsert', [StateController::class, 'upsert'])->name('world.sites.upsert');
    });

    Route::prefix('cities')->group(function () {
        Route::get('/', [CityController::class, 'index'])->name('world.cities.index');
        Route::post('/upsert', [CityController::class, 'upsert'])->name('world.cities.upsert');
        Route::delete('/{id}', [CityController::class, 'delete'])->name('world.cities.delete');
        Route::get('/install', [CityController::class, 'install'])->name('world.cities.install');
        Route::get('/search', [CityController::class, 'search'])->name('world.cities.search');
    });
});
