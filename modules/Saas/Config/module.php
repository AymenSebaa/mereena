<?php

return [
    'name'    => 'Saas',
    'slug'    => 'saas',
    'version' => '1.0.0',

    'menu' => [
        'title'    => 'SaaS',
        'icon'     => 'buildings',
        'children' => [
            [
                'title' => 'Organizations',
                'icon'  => 'diagram-3',
                'route' => 'saas.organizations.index',
            ],
            [
                'title' => 'Owners',
                'icon'  => 'people',
                'route' => 'saas.organization_users.index',
            ],
            [
                'title' => 'Plans',
                'icon'  => 'tags',
                'route' => 'saas.plans.index',
            ],
            [
                'title' => 'Invoices',
                'icon'  => 'receipt',
                'route' => 'saas.invoices.index',
            ],
            [
                'title' => 'Subscriptions',
                'icon'  => 'file-earmark-text',
                'route' => 'saas.subscriptions.index',
            ],
        ],
    ],
];
