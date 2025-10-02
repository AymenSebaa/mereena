<?php

return [
    'name'    => 'World',
    'slug'    => 'world',
    'version' => '1.0.0',

    /*
    |--------------------------------------------------------------------------
    | Sidebar / Navigation
    |--------------------------------------------------------------------------
    */
    'menu' => [
        'key'      => 'world',
        'title'    => 'World',
        'icon'     => 'globe2',
        'children' => [
            [
                'key'   => 'cities',
                'title' => 'Cities',
                'icon'  => 'buildings',
                'route' => 'world.cities.index',
            ],
            [
                'key'   => 'states',
                'title' => 'States',
                'icon'  => 'signpost-2',
                'route' => 'world.states.index',
            ],
            [
                'key'   => 'countries',
                'title' => 'Countries',
                'icon'  => 'flag',
                'route' => 'world.countries.index',
            ],
            [
                'key'   => 'regions',
                'title' => 'Regions',
                'icon'  => 'map',
                'route' => 'world.regions.index',
            ],
            [
                'key'   => 'continents',
                'title' => 'Continents',
                'icon'  => 'globe-americas',
                'route' => 'world.continents.index',
            ],
        ],
    ],
];
