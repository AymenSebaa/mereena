<?php

use App\Http\Middleware\EnsureOtpVerified;
use Illuminate\Support\Facades\Route;
use Modules\Sale\Http\Controllers\InstallController;
use Modules\Sale\Http\Controllers\OrderController;

Route::middleware(['web', 'auth', EnsureOtpVerified::class])->prefix('{organization_slug?}')->group(function () {
    
    Route::prefix('sale')->group(function () {
        // Organizations
        Route::prefix('organizations')->group(function () {
            Route::get('/', [OrderController::class, 'index'])->name('sale.organizations.index');
            Route::post('/upsert', [OrderController::class, 'upsert'])->name('sale.organizations.upsert');
            Route::delete('/{id}', [OrderController::class, 'delete'])->name('sale.organizations.delete');
        });

        Route::get('/install', [InstallController::class, 'install'])->name('sale.install');
    });
});