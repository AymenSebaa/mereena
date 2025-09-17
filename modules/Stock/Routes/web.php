<?php

use Illuminate\Support\Facades\Route;
use Modules\Stock\Http\Controllers\SupplierController;
use Modules\Stock\Http\Controllers\InventoryController;
use Modules\Stock\Http\Controllers\ProductController;

Route::middleware(['web', 'auth'])->group(function () {
    Route::prefix('products')->group(function () {
        Route::get('/', [ProductController::class, 'index'])->name('products.index');
        Route::post('/upsert', [ProductController::class, 'upsert'])->name('products.upsert');
        Route::delete('/delete/{id}', [ProductController::class, 'delete'])->name('products.delete');
    });

    Route::prefix('suppliers')->group(function () {
        Route::get('/', [SupplierController::class, 'index'])->name('suppliers.index');
        Route::post('/upsert', [SupplierController::class, 'upsert'])->name('suppliers.upsert');
        Route::delete('/delete/{id}', [SupplierController::class, 'delete'])->name('suppliers.delete');
    });

    Route::prefix('inventories')->group(function () {
        Route::get('/', [InventoryController::class, 'index'])->name('inventories.index');
        Route::post('/upsert', [InventoryController::class, 'upsert'])->name('inventories.upsert');
        Route::delete('/delete/{id}', [InventoryController::class, 'delete'])->name('inventories.delete');
    });
});
