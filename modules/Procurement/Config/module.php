<?php

return [
    'name'    => 'Procurement',
    'slug'    => 'procurement',
    'version' => '1.0.0',

    'menu' => [
        'title'    => 'Procurement',
        'icon'     => 'box-seam',
        'children' => [
            [
                'title' => 'Suppliers',
                'icon'  => 'truck',
                'route' => 'procurement.suppliers.index',
            ],
            [
                'title' => 'Warehouses',
                'icon'  => 'geo-alt',
                'route' => 'procurement.warehouses.index',
            ],
            [
                'title' => 'Purchase Orders',
                'icon'  => 'file-text',
                'route' => 'procurement.purchase_orders.index',
            ],
            [
                'title' => 'Purchase Order Items',
                'icon'  => 'card-list',
                'route' => 'procurement.purchase_order_items.index',
            ],
            [
                'title' => 'Workflows',
                'icon'  => 'diagram-3',
                'route' => 'procurement.workflows.index',
            ],
        ],
    ],
];
