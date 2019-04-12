<?php

declare(strict_types=1);

namespace App;

use App\ServiceProvider\ApplicationServiceProvider;
use App\ServiceProvider\ControllerServiceProvider;
use App\ServiceProvider\MiddlewareServiceProvider;
use Chubbyphp\Framework\Application;
use Chubbyphp\Framework\Middleware\MiddlewareDispatcher;
use Chubbyphp\Framework\ResponseHandler\JsonExceptionResponseHandler;
use Chubbyphp\Framework\Router\FastRoute\RouteDispatcher;
use Chubbyphp\Framework\Router\RouteCollection;
use Pimple\Container;

require __DIR__.'/bootstrap.php';

/** @var Container $container */
$container = require __DIR__.'/container.php';
$container->register(new ApplicationServiceProvider());
$container->register(new ControllerServiceProvider());
$container->register(new MiddlewareServiceProvider());

$app = new Application(
    new RouteDispatcher($container[RouteCollection::class], $container['cacheDir']),
    new MiddlewareDispatcher(),
    new JsonExceptionResponseHandler($container['api-http.response.factory'], $container['debug'])
);

return $app;
