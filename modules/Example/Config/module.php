<?php

// to skip it.
return [];

return [
    'name'    => 'Example',
    'slug'    => 'example',
    'version' => '1.0.0',

    'menu' => [
        'title'    => 'Example',
        'icon'     => 'icon',
        'children' => [
            [
                'title' => 'Title',
                'icon'  => 'icon',
                'route' => 'example.items.index',
            ],
            // Add more menu items as needed
        ],
    ],
];
