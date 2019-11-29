<?php

declare(strict_types=1);

namespace App;

use App\Config\DevConfig;
use App\Config\PhpunitConfig;
use App\Config\ProdConfig;
use App\ServiceProvider\ChubbyphpFrameworkProvider;
use App\ServiceProvider\MiddlewareServiceProvider;
use App\ServiceProvider\RequestHandlerServiceProvider;
use Chubbyphp\Config\ConfigProvider;
use Chubbyphp\Config\ServiceProvider\ConfigServiceProvider;
use Chubbyphp\Cors\CorsMiddleware;
use Chubbyphp\Framework\Application;
use Chubbyphp\Framework\ErrorHandler;
use Chubbyphp\Framework\Middleware\ExceptionMiddleware;
use Chubbyphp\Framework\Middleware\LazyMiddleware;
use Chubbyphp\Framework\Middleware\RouterMiddleware;
use Pimple\Container;
use Pimple\Psr11\Container as PsrContainer;

require __DIR__.'/../vendor/autoload.php';

return static function (string $env) {
    set_error_handler([new ErrorHandler(), 'errorToException']);

    /** @var Container $container */
    $container = (require __DIR__.'/container.php')();
    $container->register(new MiddlewareServiceProvider());
    $container->register(new RequestHandlerServiceProvider());
    $container->register(new ChubbyphpFrameworkProvider());

    // always load this service provider last
    // so that the values of other service providers can be overwritten.
    $container->register(new ConfigServiceProvider(
        (new ConfigProvider([
            new DevConfig(__DIR__.'/..'),
            new PhpunitConfig(__DIR__.'/..'),
            new ProdConfig(__DIR__.'/..'),
        ]))->get($env)
    ));

    return new Application([
        new LazyMiddleware($container[PsrContainer::class], ExceptionMiddleware::class),
        new LazyMiddleware($container[PsrContainer::class], CorsMiddleware::class),
        new LazyMiddleware($container[PsrContainer::class], RouterMiddleware::class),
    ]);
};
