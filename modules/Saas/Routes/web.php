<?php

use App\Http\Middleware\EnsureOtpVerified;
use Illuminate\Support\Facades\Route;
use Modules\Saas\Http\Controllers\InstallController;
use Modules\Saas\Http\Controllers\OrganizationController;
use Modules\Saas\Http\Controllers\OrganizationUserController;
use Modules\Saas\Http\Controllers\PlanController;
use Modules\Saas\Http\Controllers\SubscriptionController;
use Modules\Saas\Http\Controllers\InvoiceController;

Route::middleware(['web', 'auth', EnsureOtpVerified::class])->prefix('{organization_slug?}')->group(function () {
    
    Route::prefix('saas')->group(function () {
        // Organizations
        Route::prefix('organizations')->group(function () {
            Route::get('/', [OrganizationController::class, 'index'])->name('saas.organizations.index');
            Route::post('/upsert', [OrganizationController::class, 'upsert'])->name('saas.organizations.upsert');
            Route::delete('/{id}', [OrganizationController::class, 'delete'])->name('saas.organizations.delete');
        });

        // Organization Users
        Route::prefix('organization-users')->group(function () {
            Route::get('/', [OrganizationUserController::class, 'index'])->name('saas.organization_users.index');
            Route::post('/upsert', [OrganizationUserController::class, 'upsert'])->name('saas.organization_users.upsert');
            Route::delete('/{id}', [OrganizationUserController::class, 'delete'])->name('saas.organization_users.delete');
        });

        // Plans
        Route::prefix('plans')->group(function () {
            Route::get('/', [PlanController::class, 'index'])->name('saas.plans.index');
            Route::post('/upsert', [PlanController::class, 'upsert'])->name('saas.plans.upsert');
            Route::delete('/{id}', [PlanController::class, 'delete'])->name('saas.plans.delete');
        });

        // Subscriptions
        Route::prefix('subscriptions')->group(function () {
            Route::get('/', [SubscriptionController::class, 'index'])->name('saas.subscriptions.index');
            Route::post('/upsert', [SubscriptionController::class, 'upsert'])->name('saas.subscriptions.upsert');
            Route::delete('/{id}', [SubscriptionController::class, 'delete'])->name('saas.subscriptions.delete');
        });

        // Invoices
        Route::prefix('invoices')->group(function () {
            Route::get('/', [InvoiceController::class, 'index'])->name('saas.invoices.index');
            Route::post('/upsert', [InvoiceController::class, 'upsert'])->name('saas.invoices.upsert');
            Route::delete('/{id}', [InvoiceController::class, 'delete'])->name('saas.invoices.delete');
        });

        Route::get('/install', [InstallController::class, 'install'])->name('saas.install');
    });
});
