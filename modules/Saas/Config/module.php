<?php

return [
    'name'    => 'Saas',
    'slug'    => 'saas',
    'version' => '1.0.0',

    /*
    |--------------------------------------------------------------------------
    | Sidebar / Navigation
    |--------------------------------------------------------------------------
    */
    'menu' => [
        'key'      => 'saas',
        'title'    => 'SaaS',
        'icon'     => 'buildings',
        'children' => [
            [
                'key'   => 'organizations',
                'title' => 'Organizations',
                'icon'  => 'diagram-3',
                'route' => 'saas.organizations.index',
            ],
            [
                'key'   => 'owners',
                'title' => 'Owners',
                'icon'  => 'people',
                'route' => 'saas.organization_users.index',
            ],
            [
                'key'   => 'plans',
                'title' => 'Plans',
                'icon'  => 'tags',
                'route' => 'saas.plans.index',
            ],
            [
                'key'   => 'invoices',
                'title' => 'Invoices',
                'icon'  => 'receipt',
                'route' => 'saas.invoices.index',
            ],
            [
                'key'   => 'subscriptions',
                'title' => 'Subscriptions',
                'icon'  => 'file-earmark-text',
                'route' => 'saas.subscriptions.index',
            ],
        ],
    ],
];
