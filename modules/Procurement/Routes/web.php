<?php

use App\Http\Middleware\EnsureOtpVerified;
use Illuminate\Support\Facades\Route;
use Modules\Procurement\Http\Controllers\InstallController;
use Modules\Procurement\Http\Controllers\SupplierController;
use Modules\Procurement\Http\Controllers\PurchaseOrderController;
use Modules\Procurement\Http\Controllers\PurchaseOrderItemController;
use Modules\Procurement\Http\Controllers\WarehouseController;
use Modules\Procurement\Http\Controllers\WorkflowController;

Route::middleware(['web', 'auth', EnsureOtpVerified::class])
    ->prefix('{organization_slug?}')
    ->group(function () {

        Route::prefix('procurement')->group(function () {

            // CRUD routes using helper
            udsRoutes('suppliers', SupplierController::class, 'procurement.suppliers');
            udsRoutes('purchase-orders', PurchaseOrderController::class, 'procurement.purchase_orders');
            udsRoutes('purchase-order-items', PurchaseOrderItemController::class, 'procurement.purchase_order_items');
            udsRoutes('warehouses', WarehouseController::class, 'procurement.warehouses');
            udsRoutes('workflows', WorkflowController::class, 'procurement.workflows');

            // Install route
            Route::get('/install', [InstallController::class, 'install'])->name('procurement.install');
        });
    });
