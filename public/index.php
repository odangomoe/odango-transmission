<?php

use Odango\Transmission\Container;
use Odango\Transmission\Handler\Api;
use Odango\Transmission\Handler\Home;
use Slim\Factory\AppFactory;

include_once __DIR__.'/../vendor/autoload.php';

$container = Container::get();
$app       = AppFactory::createFromContainer($container);

$app->get(
    '/',
    function ($request, $response) use ($container) {
        return $container->get(Home::class)->home($request, $response);
    }
);

$app->get(
    '/torrents/{id}/{selected-name}',
    function ($request, $response, $vars) use ($container) {
        return $container->get(Home::class)->torrent($request, $response, $vars);
    }
);

$app->post(
    '/api/subscribe',
    function ($request, $response) use ($container) {
        return $container->get(Api::class)->subscribe($request, $response);
    }
);

$app->post(
    '/api/unsubscribe',
    function ($request, $response) use ($container) {
        return $container->get(Api::class)->unsubscribe($request, $response);
    }
);

$app->run();