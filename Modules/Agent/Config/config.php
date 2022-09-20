<?php

return [
    'name' => 'Agent',
    'item_id' => 'wr6h7efkefa',
    'options' => [
        ['label' => 'Settings', 'url' => '#']
    ],
    'route_group' => [
        'authenticated' => [
            'agent' => [
                'prefix' => 'agent',
                'middleware' => ['guest:agent', 'locale']
            ],
        ],
        'unauthenticated' => [
            'agent' => [
                'prefix' => 'agent',
                'middleware' => ['no_auth:agent', 'locale']
            ],
        ]
    ],
    'guards' => [
        'agent' => [
            'driver'   => 'session',
            'provider' => 'agent',
        ],
    ],
    'providers' => [
        'agent' => [
            'driver' => 'eloquent',
            'model'  => Modules\Agent\Entities\Agent::class,
            'table'  => 'agents',
        ],
    ],
    'passwords' => [
        'agent' => [
            'provider' => 'agent',
            'table'    => 'password_resets',
            'expire'   => 60,
            'throttle' => 60,
        ],
    ],
    'file_permission' => 755,

    'config_upload' => true,
];
