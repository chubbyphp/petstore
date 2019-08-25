<?php

declare(strict_types=1);

namespace App;

use App\ServiceProvider\ChubbyphpFrameworkProvider;
use App\ServiceProvider\MiddlewareServiceProvider;
use App\ServiceProvider\RequestHandlerServiceProvider;
use Chubbyphp\Framework\Application;
use Chubbyphp\Framework\Middleware\ExceptionMiddleware;
use Chubbyphp\Framework\Middleware\LazyMiddleware;
use Chubbyphp\Framework\Middleware\RouterMiddleware;
use Pimple\Container;
use Pimple\Psr11\Container as PsrContainer;

require __DIR__.'/bootstrap.php';

/** @var Container $container */
$container = require __DIR__.'/container.php';
$container->register(new MiddlewareServiceProvider());
$container->register(new RequestHandlerServiceProvider());
$container->register(new ChubbyphpFrameworkProvider());

$web = new Application([
    new LazyMiddleware($container[PsrContainer::class], ExceptionMiddleware::class),
    new LazyMiddleware($container[PsrContainer::class], RouterMiddleware::class),
]);

return $web;
