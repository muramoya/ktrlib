<?php

return [
    'paths' => [
        'migrations' => APP_BASE_PATH . '/database/migrations',
        'seeds' => APP_BASE_PATH . '/database/seeds'
    ],
    'environments' => [
        'default_migration_table' => 'migration',
        'default_database' => 'dev',
        'dev' => [
            'adapter' => env('DATABASE_DRIVER'),
            'host' => env('DATABASE_HOST'),
            'name' => env('DATABASE_DBNAME'),
            'user' => env('DATABASE_USER'),
            'pass' => env('DATABASE_PASSWORD'),
            'port' => env('DATABASE_PORT')
        ]
    ]
];