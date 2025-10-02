<?php

return [
    'name'    => 'Stock',
    'slug'    => 'stock',
    'version' => '1.0.0',

    /*
    |--------------------------------------------------------------------------
    | Sidebar / Navigation
    |--------------------------------------------------------------------------
    */
    'menu' => [
        'key'      => 'stock',
        'title'    => 'Stock',
        'icon'     => 'boxes',
        'children' => [
            [
                'key'   => 'inventories',
                'title' => 'Inventories',
                'icon'  => 'dropbox',
                'route' => 'stock.inventories.index',
            ],
            [
                'key'   => 'products',
                'title' => 'Products',
                'icon'  => 'box-seam',
                'route' => 'stock.products.index',
            ],
        ],
    ],
];
