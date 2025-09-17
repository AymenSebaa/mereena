<?php

return [
    'name'    => 'Stock',
    'slug'    => 'stock',
    'version' => '1.0.0',

    // Sidebar / Navigation definition
    'menu' => [
        'title'    => 'Stock',
        'icon'     => 'boxes', // FontAwesome or Lucide icon
        'route'    => 'inventories.index',
        'children' => [
            [
                'title' => 'Inventory',
                'icon'  => 'warehouse',
                'route' => 'inventories.index',
            ],
            [
                'title' => 'Products',
                'icon'  => 'box',
                'route' => 'products.index',
            ],
            [
                'title' => 'Suppliers',
                'icon'  => 'truck',
                'route' => 'suppliers.index',
            ],
        ],
    ],

    // Optional: permissions for RBAC
    'permissions' => [
        'view_products',
        'create_products',
        'edit_products',
        'delete_products',

        'view_inventory',
        'update_inventory',

        'view_suppliers',
        'create_suppliers',
        'edit_suppliers',
        'delete_suppliers',
    ],
];
