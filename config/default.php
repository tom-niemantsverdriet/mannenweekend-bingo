<?php

$configuration = [
    'database' => [
        'host' => 'localhost',
        'user' => 'root',
        'password' => '',
        'database' => 'database'
    ],

    'cache' => 'disk',
    'debug' => true,

    'plugins' => [
        'common' => 1.0,
        'bootstrap' => 1.0,
        'sense' => 1.0,

        // The Vue application is served from the main application and the JSON
        // API runs in its own mount. Both load the flowtogether-vue package
        // that provides the Vue tooling.

        'flowtogether-vue' => 1.0,

        'app-api' => [
            'flowtogether-vue' => 1.0
        ],
    ],

    'analytics' => null,
    'mail-exceptions' => null,
];
