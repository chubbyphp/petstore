<?php

declare(strict_types=1);

namespace App;

use App\Controller\Crud\CreateController;
use App\Controller\Crud\DeleteController;
use App\Controller\Crud\ListController;
use App\Controller\Crud\ReadController;
use App\Controller\Crud\UpdateController;
use App\Controller\IndexController;
use App\Controller\PingController;
use App\Controller\Swagger\IndexController as SwaggerIndexController;
use App\Controller\Swagger\YamlController as SwaggerYamlController;
use App\Middleware\AcceptAndContentTypeMiddleware;
use App\Model\Pet;
use App\ServiceProvider\ControllerServiceProvider;
use App\ServiceProvider\MiddlewareServiceProvider;
use Chubbyphp\Framework\Application;
use Chubbyphp\Framework\Middleware\LazyMiddleware;
use Chubbyphp\Framework\Middleware\MiddlewareDispatcher;
use Chubbyphp\Framework\RequestHandler\LazyRequestHandler;
use Chubbyphp\Framework\ResponseHandler\ExceptionResponseHandler;
use Chubbyphp\Framework\Router\FastRoute\RouteDispatcher;
use Chubbyphp\Framework\Router\FastRoute\UrlGenerator;
use Chubbyphp\Framework\Router\RouteCollection;
use Chubbyphp\Framework\Router\RouteInterface;
use Pimple\Container;
use Pimple\Psr11\Container as PsrContainer;

require __DIR__.'/bootstrap.php';

/** @var Container $container */
$container = require __DIR__.'/container.php';
$container->register(new ControllerServiceProvider());
$container->register(new MiddlewareServiceProvider());

$container[RouteCollection::class] = function () use ($container) {
    $psrContainer = new PsrContainer($container);

    $acceptAndContentTypeMiddleware = new LazyMiddleware($psrContainer, AcceptAndContentTypeMiddleware::class);

    return (new RouteCollection())
        ->route('/', RouteInterface::GET, 'index', new LazyRequestHandler($psrContainer, IndexController::class))
        ->group('/api')
            ->route('', RouteInterface::GET, 'swagger_index',
                new LazyRequestHandler($psrContainer, SwaggerIndexController::class)
            )
            ->route('/swagger.yml', RouteInterface::GET, 'swagger_yml',
                new LazyRequestHandler($psrContainer, SwaggerYamlController::class)
            )
            ->route('/ping', RouteInterface::GET, 'ping',
                new LazyRequestHandler($psrContainer, PingController::class),
                [$acceptAndContentTypeMiddleware]
            )
            ->group('/pets', [$acceptAndContentTypeMiddleware])
                ->route('', RouteInterface::GET, 'pet_list',
                    new LazyRequestHandler($psrContainer, ListController::class.Pet::class)
                )
                ->route('', RouteInterface::POST, 'pet_create',
                    new LazyRequestHandler($psrContainer, CreateController::class.Pet::class)
                )
                ->route('/{id}', RouteInterface::GET, 'pet_read',
                    new LazyRequestHandler($psrContainer, ReadController::class.Pet::class)
                )
                ->route('/{id}', RouteInterface::PUT, 'pet_update',
                    new LazyRequestHandler($psrContainer, UpdateController::class.Pet::class)
                )
                ->route('/{id}', RouteInterface::DELETE, 'pet_delete',
                    new LazyRequestHandler($psrContainer, DeleteController::class.Pet::class)
                )
            ->end()
        ->end()
    ;
};

$container[UrlGenerator::class] = function () use ($container) {
    return new UrlGenerator($container[RouteCollection::class]);
};

$app = new Application(
    new RouteDispatcher($container[RouteCollection::class], $container['cacheDir']),
    new MiddlewareDispatcher(),
    new ExceptionResponseHandler($container['api-http.response.factory'], $container['debug'])
);

return $app;
