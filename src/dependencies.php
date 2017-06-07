<?php
// DIC configuration

use League\Flysystem\Adapter\Local;
use League\Flysystem\Filesystem;
use Cache\Adapter\Filesystem\FilesystemCachePool;
use GuzzleHttp\Client;
use Illuminate\Database\Capsule\Manager as Capsule;

$container = $app->getContainer();

// monolog
$container['logger'] = function ($container) {
    $settings = $container->get('settings')['logger'];
    $logger = new Monolog\Logger($settings['name']);
    $logger->pushProcessor(new Monolog\Processor\UidProcessor());
    $logger->pushHandler(new Monolog\Handler\StreamHandler($settings['path'], $settings['level']));
    return $logger;
};

// http client (guzzle)
$container['httpclient'] = function ($container) {
    return new Client();
};

// phpcache
$container['cache'] = function ($container) {
    $settings = $container->get('settings')['cache'];

    $filesystemAdapter = new Local($settings['path']);
    $filesystem        = new Filesystem($filesystemAdapter);

    return new FilesystemCachePool($filesystem);
};

// eloquent ORM
$capsule = new Capsule;

$capsule->addConnection($container->get('settings')['database']);

// Make this Capsule instance available globally via static methods
$capsule->setAsGlobal();

// Setup the Eloquent ORM
$capsule->bootEloquent();
