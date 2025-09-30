<?php

use App\Http\Middleware\EnsureOtpVerified;
use Illuminate\Support\Facades\Route;
use Modules\Stock\Http\Controllers\ProductController;
use Modules\Stock\Http\Controllers\SupplierController;
use Modules\Stock\Http\Controllers\InventoryController;
use Modules\Stock\Http\Controllers\OrderController;

Route::middleware(['web', 'auth', EnsureOtpVerified::class])
    ->prefix('{organization_slug?}')
    ->group(function () {

        Route::prefix('stock')->group(function () {

            // CRUD routes using helper
            udsRoutes('products', ProductController::class, 'stock.products');
            udsRoutes('suppliers', SupplierController::class, 'stock.suppliers');
            udsRoutes('inventories', InventoryController::class, 'stock.inventories');
            udsRoutes('orders', OrderController::class, 'stock.orders');

            // Additional route: fetch inventories by product
            Route::get('inventories/by-product/{productId}', [InventoryController::class, 'byProduct'])
                ->name('stock.inventories.byProduct');
        });
    });
