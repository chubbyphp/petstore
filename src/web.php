<?php

declare(strict_types=1);

namespace App;

use App\Core\RequestHandler\OpenapiRequestHandler;
use App\Core\RequestHandler\PingRequestHandler;
use App\Pet\Model\Pet;
use Chubbyphp\Api\Middleware\ApiExceptionMiddleware;
use Chubbyphp\Api\RequestHandler\CreateRequestHandler;
use Chubbyphp\Api\RequestHandler\DeleteRequestHandler;
use Chubbyphp\Api\RequestHandler\ListRequestHandler;
use Chubbyphp\Api\RequestHandler\ReadRequestHandler;
use Chubbyphp\Api\RequestHandler\UpdateRequestHandler;
use Chubbyphp\Cors\CorsMiddleware;
use Chubbyphp\Negotiation\Middleware\AcceptMiddleware;
use Chubbyphp\Negotiation\Middleware\ContentTypeMiddleware;
use Laminas\HttpHandlerRunner\RequestHandlerRunner;
use Laminas\Stratigility\Middleware\ErrorHandler;
use Laminas\Stratigility\MiddlewarePipeInterface;
use Mezzio\Application;
use Mezzio\Handler\NotFoundHandler;
use Mezzio\MiddlewareFactory;
use Mezzio\Router\Middleware\DispatchMiddleware;
use Mezzio\Router\Middleware\MethodNotAllowedMiddleware;
use Mezzio\Router\Middleware\RouteMiddleware;
use Mezzio\Router\RouteCollector;
use Psr\Container\ContainerInterface;

require __DIR__.'/../vendor/autoload.php';

return static function (string $env) {
    /** @var ContainerInterface $container */
    $container = (require __DIR__.'/container.php')($env);

    /** @var MiddlewareFactory $middlewareFactory */
    $middlewareFactory = $container->get(MiddlewareFactory::class);

    /** @var MiddlewarePipeInterface $middlewarePipeline */
    $middlewarePipeline = $container->get('Mezzio\ApplicationPipeline');

    /** @var RouteCollector $routeCollector */
    $routeCollector = $container->get(RouteCollector::class);

    /** @var RequestHandlerRunner $requestHandlerRunner */
    $requestHandlerRunner = $container->get(RequestHandlerRunner::class);

    $web = new Application(
        $middlewareFactory,
        $middlewarePipeline,
        $routeCollector,
        $requestHandlerRunner
    );

    $web->pipe(ErrorHandler::class);
    $web->pipe(CorsMiddleware::class);
    $web->pipe(RouteMiddleware::class);
    $web->pipe(MethodNotAllowedMiddleware::class);
    $web->pipe(DispatchMiddleware::class);
    $web->pipe(NotFoundHandler::class);

    $apiMiddlewares = [
        AcceptMiddleware::class,
        ApiExceptionMiddleware::class,
    ];

    $web->get('/openapi', OpenapiRequestHandler::class, 'openapi');
    $web->get('/ping', PingRequestHandler::class, 'ping');
    $web->get('/api/pets', [
        ...$apiMiddlewares,
        Pet::class.ListRequestHandler::class,
    ], 'pet_list');
    $web->post('/api/pets', [
        ...$apiMiddlewares,
        ContentTypeMiddleware::class,
        Pet::class.CreateRequestHandler::class,
    ], 'pet_create');
    $web->get('/api/pets/{id}', [
        ...$apiMiddlewares,
        Pet::class.ReadRequestHandler::class,
    ], 'pet_read');
    $web->put('/api/pets/{id}', [
        ...$apiMiddlewares,
        ContentTypeMiddleware::class,
        Pet::class.UpdateRequestHandler::class,
    ], 'pet_update');
    $web->delete('/api/pets/{id}', [
        ...$apiMiddlewares,
        Pet::class.DeleteRequestHandler::class,
    ], 'pet_delete');

    return $web;
};
