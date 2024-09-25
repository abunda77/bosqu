<?php

return [
    'navigation' => [
        'token' => [
            'cluster' => null,
            'group' => 'Admin',
            'sort' => -1,
            'icon' => 'heroicon-o-key',
        ],
    ],
    'models' => [
        'token' => [
            'enable_policy' => true,
        ],
    ],
    'route' => [
        'panel_prefix' => false,
        'use_resource_middlewares' => true,
    ],
    'tenancy' => [
        'enabled' => false,
        'awareness' => false,
    ],
];
