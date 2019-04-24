<?php

declare(strict_types=1);

namespace App;

use App\ServiceProvider\ControllerServiceProvider;
use App\ServiceProvider\MiddlewareServiceProvider;
use App\ServiceProvider\RouterServiceProvider;
use Chubbyphp\Framework\Application;
use Chubbyphp\Framework\Middleware\MiddlewareDispatcher;
use Chubbyphp\Framework\ResponseHandler\JsonExceptionResponseHandler;
use Chubbyphp\Framework\Router\FastRoute\RouteDispatcher;
use Pimple\Container;

require __DIR__.'/bootstrap.php';

/** @var Container $container */
$container = require __DIR__.'/container.php';
$container->register(new ControllerServiceProvider());
$container->register(new MiddlewareServiceProvider());
$container->register(new RouterServiceProvider());

$app = new Application(
    new RouteDispatcher($container['routes'], $container['cacheDir']),
    new MiddlewareDispatcher(),
    new JsonExceptionResponseHandler($container['api-http.response.factory'], $container['debug'])
);

return $app;
