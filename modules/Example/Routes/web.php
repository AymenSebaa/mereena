<?php

use App\Http\Middleware\EnsureOtpVerified;
use Illuminate\Support\Facades\Route;
use Modules\Example\Http\Controllers\ItemController;
use Modules\Example\Http\Controllers\InstallController;

Route::middleware(['web', 'auth', EnsureOtpVerified::class])->prefix('{organization_slug?}')->group(function () {
    
    Route::prefix('saas')->group(function () {
        // Organizations
        Route::prefix('items')->group(function () {
            Route::get('/', [ItemController::class, 'index'])->name('example.items.index');
            Route::post('/upsert', [ItemController::class, 'upsert'])->name('example.items.upsert');
            Route::delete('/delete/{id}', [ItemController::class, 'delete'])->name('example.items.delete');
        });

        Route::get('/install', [InstallController::class, 'install'])->name('example.install');
    });
});

