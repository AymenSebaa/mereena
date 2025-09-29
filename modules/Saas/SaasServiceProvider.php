<?php

namespace Modules\Saas;

use Illuminate\Support\ServiceProvider;
use App\Models\User;
use Modules\Saas\Models\Organization;
use Modules\Saas\Models\OrganizationUser;

class SaasServiceProvider extends ServiceProvider {
    public function boot() {
        // Load routes
        $this->loadRoutesFrom(__DIR__ . '/Routes/web.php');
        $this->loadRoutesFrom(__DIR__ . '/Routes/api.php');

        // Load migrations
        $this->loadMigrationsFrom(__DIR__ . '/Database/Migrations');

        // Load views
        $this->loadViewsFrom(__DIR__ . '/Views', 'saas');

        // Publish config
        $this->mergeConfigFrom(__DIR__ . '/Config/module.php', 'modules.saas');

        // Add dynamic relation "organization" to User model
        User::resolveRelationUsing('organization', function ($userModel) {
            return $userModel->hasOneThrough(
                Organization::class,
                OrganizationUser::class,
                'user_id',        // Foreign key on OrganizationUser table...
                'id',             // Foreign key on Organization table...
                'id',             // Local key on User table...
                'organization_id' // Local key on OrganizationUser table...
            );
        });

        // Optionally also add "organizationUser" for role & pivot data
        User::resolveRelationUsing('organizationUser', function ($userModel) {
            return $userModel->hasOne(OrganizationUser::class, 'user_id');
        });
    }

    public function register() {
        //
    }
}
