<?php

namespace App\Traits;

use Modules\Saas\Traits\BelongsToOrganization as BelongsToOrganizationTrait;

if (trait_exists(BelongsToOrganizationTrait::class)) {
    class_alias(BelongsToOrganizationTrait::class, 'App\Traits\BelongsToOrganization');
} else {
    trait BelongsToOrganization {}
}