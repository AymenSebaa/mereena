<?php

// to skip it.
return [];

return [
    'name'    => 'Sale',
    'slug'    => 'sale',
    'version' => '1.0.0',

    'menu' => [
        'title'    => 'Sale',
        'icon'     => 'icon',
        'children' => [
            [
                'key' => 'orders',
                'title' => 'Orders',
                'icon'  => 'receipt',
                'route' => 'stock.orders.index',
            ],
            // Add more menu items as needed
        ],
    ],
];
