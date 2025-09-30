<?php

return [
    'name'    => 'World',
    'slug'    => 'world',
    'version' => '1.0.0',

    'menu' => [
        'title'    => 'World',
        'icon'     => 'globe2',
        'children' => [
            [
                'title' => 'Cities',
                'icon'  => 'buildings',
                'route' => 'world.cities.index',
            ],
            [
                'title' => 'States',
                'icon'  => 'signpost-2',
                'route' => 'world.states.index',
            ],
            [
                'title' => 'Countries',
                'icon'  => 'flag',
                'route' => 'world.countries.index',
            ],
            [
                'title' => 'Regions',
                'icon'  => 'map',
                'route' => 'world.regions.index',
            ],
            [
                'title' => 'Continents',
                'icon'  => 'globe-americas',
                'route' => 'world.continents.index',
            ],

        ],
    ],
];
