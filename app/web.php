<?php

declare(strict_types=1);

namespace App;

use App\ServiceProvider\ChubbyphpFrameworkProvider;
use App\ServiceProvider\MiddlewareServiceProvider;
use App\ServiceProvider\RequestHandlerServiceProvider;
use Chubbyphp\Framework\Application;
use Chubbyphp\Framework\ExceptionHandler;
use Chubbyphp\Framework\Middleware\MiddlewareDispatcher;
use Chubbyphp\Framework\Router\FastRouteRouter;
use Pimple\Container;

require __DIR__.'/bootstrap.php';

/** @var Container $container */
$container = require __DIR__.'/container.php';
$container->register(new MiddlewareServiceProvider());
$container->register(new RequestHandlerServiceProvider());
$container->register(new ChubbyphpFrameworkProvider());

$web = new Application(
    $container[FastRouteRouter::class],
    $container[MiddlewareDispatcher::class],
    $container[ExceptionHandler::class]
);

return $web;
