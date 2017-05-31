<?php
require_once __DIR__.'/../../helper.php';

return [
    'paths' => [
        'migrations' => __DIR__ . '/../../../database/migrations',
        'seeds' => __DIR__ . '/../../../database/seeds'
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