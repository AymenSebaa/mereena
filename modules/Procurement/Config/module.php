<?php

return [
    'name'    => 'Procurement',
    'slug'    => 'procurement',
    'version' => '1.0.0',

    /*
    |--------------------------------------------------------------------------
    | Sidebar / Navigation
    |--------------------------------------------------------------------------
    */
    'menu' => [
        'key'      => 'procurement',
        'title'    => 'Procurement',
        'icon'     => 'box-seam',
        'children' => [
            [
                'key'   => 'suppliers',
                'title' => 'Suppliers',
                'icon'  => 'truck',
                'route' => 'procurement.suppliers.index',
            ],
            [
                'key'   => 'warehouses',
                'title' => 'Warehouses',
                'icon'  => 'geo-alt',
                'route' => 'procurement.warehouses.index',
            ],
            [
                'key'   => 'purchase_orders',
                'title' => 'Purchase Orders',
                'icon'  => 'file-text',
                'route' => 'procurement.purchase_orders.index',
            ],
            [
                'key'   => 'purchase_order_items',
                'title' => 'Purchase Order Items',
                'icon'  => 'card-list',
                'route' => 'procurement.purchase_order_items.index',
            ],
            [
                'key'   => 'workflows',
                'title' => 'Workflows',
                'icon'  => 'diagram-3',
                'route' => 'procurement.workflows.index',
            ],
        ],
    ],
];
