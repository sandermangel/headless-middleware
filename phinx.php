<?php

require __DIR__ . '/src/bootstrap.php';

/** @var array $settings */
return ['paths' => [
        'migrations' => __DIR__ . '/db/migrations',
        'seeds' => __DIR__ . '/db/seeds',
    ],
    'environments' => [
        'default_database' => 'development',
        'development' => [
            'adapter' => $settings['settings']['database']['driver'],
            'host' => $settings['settings']['database']['host'],
            'name' => $settings['settings']['database']['database'],
            'user' => $settings['settings']['database']['username'],
            'pass' => $settings['settings']['database']['password'],
            'port' => '3306',
            'charset' => 'utf8',
        ],
    ]
];