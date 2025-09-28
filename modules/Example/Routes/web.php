<?php

use Illuminate\Support\Facades\Route;
use Modules\Stock\Http\Controllers\ItemController;

Route::prefix('example')->middleware(['web', 'auth'])->group(function () {
    // Items
    Route::prefix('items')->group(function () {
        Route::get('/', [ItemController::class, 'index'])->name('example.items.index');
        Route::post('/upsert', [ItemController::class, 'upsert'])->name('example.items.upsert');
        Route::delete('/delete/{id}', [ItemController::class, 'delete'])->name('example.items.delete');
    });
});
