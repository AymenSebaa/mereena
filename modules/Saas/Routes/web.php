<?php

use App\Http\Middleware\EnsureOtpVerified;
use Illuminate\Support\Facades\Route;
use Modules\Saas\Http\Controllers\InstallController;
use Modules\Saas\Http\Controllers\OrganizationController;
use Modules\Saas\Http\Controllers\OrganizationUserController;
use Modules\Saas\Http\Controllers\PlanController;
use Modules\Saas\Http\Controllers\SubscriptionController;
use Modules\Saas\Http\Controllers\InvoiceController;

Route::middleware(['web', 'auth', EnsureOtpVerified::class])
    ->prefix('{organization_slug?}')
    ->group(function () {

        Route::prefix('saas')->group(function () {

            // CRUD routes using helper
            udsRoutes('organizations', OrganizationController::class, 'saas.organizations');
            udsRoutes('organization-users', OrganizationUserController::class, 'saas.organization_users');
            udsRoutes('plans', PlanController::class, 'saas.plans');
            udsRoutes('subscriptions', SubscriptionController::class, 'saas.subscriptions');
            udsRoutes('invoices', InvoiceController::class, 'saas.invoices');

            // Install route
            Route::get('/install', [InstallController::class, 'install'])->name('saas.install');
        });
    });
