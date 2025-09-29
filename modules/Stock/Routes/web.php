<?php

use App\Http\Middleware\EnsureOtpVerified;
use Illuminate\Support\Facades\Route;
use Modules\Stock\Http\Controllers\ProductController;
use Modules\Stock\Http\Controllers\SupplierController;
use Modules\Stock\Http\Controllers\InventoryController;
use Modules\Stock\Http\Controllers\OrderController;

Route::middleware(['web', 'auth', EnsureOtpVerified::class])->prefix('{organization_slug?}')->group(function () {

    Route::prefix('stock')->group(function () {
        // Products
        Route::prefix('products')->group(function () {
            Route::get('/', [ProductController::class, 'index'])->name('stock.products.index');
            Route::post('/upsert', [ProductController::class, 'upsert'])->name('stock.products.upsert');
            Route::delete('/delete/{id}', [ProductController::class, 'delete'])->name('stock.products.delete');
        });

        // Suppliers
        Route::prefix('suppliers')->group(function () {
            Route::get('/', [SupplierController::class, 'index'])->name('stock.suppliers.index');
            Route::post('/upsert', [SupplierController::class, 'upsert'])->name('stock.suppliers.upsert');
            Route::delete('/delete/{id}', [SupplierController::class, 'delete'])->name('stock.suppliers.delete');
        });

        // Inventories
        Route::prefix('inventories')->group(function () {
            Route::get('/', [InventoryController::class, 'index'])->name('stock.inventories.index');
            Route::post('/upsert', [InventoryController::class, 'upsert'])->name('stock.inventories.upsert');
            Route::delete('/delete/{id}', [InventoryController::class, 'delete'])->name('stock.inventories.delete');

            // fetch inventories for a specific product (used in order modal)
            Route::get('/by-product/{productId}', [InventoryController::class, 'byProduct'])->name('stock.inventories.byProduct');
        });

        // Orders
        Route::prefix('orders')->group(function () {
            Route::get('/', [OrderController::class, 'index'])->name('stock.orders.index');
            Route::post('/upsert', [OrderController::class, 'upsert'])->name('stock.orders.upsert');
            Route::delete('/delete/{id}', [OrderController::class, 'delete'])->name('stock.orders.delete');
        });
    });
});
