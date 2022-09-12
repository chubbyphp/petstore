<?php

declare(strict_types=1);

namespace App;

use App\Model\Pet;
use App\RequestHandler\Api\Crud\CreateRequestHandler;
use App\RequestHandler\Api\Crud\DeleteRequestHandler;
use App\RequestHandler\Api\Crud\ListRequestHandler;
use App\RequestHandler\Api\Crud\ReadRequestHandler;
use App\RequestHandler\Api\Crud\UpdateRequestHandler;
use App\RequestHandler\OpenapiRequestHandler;
use App\RequestHandler\PingRequestHandler;
use Chubbyphp\ApiHttp\Middleware\AcceptAndContentTypeMiddleware;
use Chubbyphp\ApiHttp\Middleware\ApiExceptionMiddleware;
use Chubbyphp\Cors\CorsMiddleware;
use Laminas\HttpHandlerRunner\RequestHandlerRunner;
use Laminas\Stratigility\Middleware\ErrorHandler;
use Mezzio\Application;
use Mezzio\ApplicationPipeline;
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

    $web = new Application(
        $container->get(MiddlewareFactory::class),
        $container->get(ApplicationPipeline::class),
        $container->get(RouteCollector::class),
        $container->get(RequestHandlerRunner::class)
    );

    $web->pipe(ErrorHandler::class);
    $web->pipe(CorsMiddleware::class);
    $web->pipe(RouteMiddleware::class);
    $web->pipe(MethodNotAllowedMiddleware::class);
    $web->pipe(DispatchMiddleware::class);
    $web->pipe(NotFoundHandler::class);

    $web->get('/openapi', OpenapiRequestHandler::class, 'openapi');
    $web->get('/ping', PingRequestHandler::class, 'ping');
    $web->get('/api/pets', [
        AcceptAndContentTypeMiddleware::class,
        Pet::class.ListRequestHandler::class,
    ], 'pet_list');
    $web->post('/api/pets', [
        AcceptAndContentTypeMiddleware::class,
        ApiExceptionMiddleware::class,
        Pet::class.CreateRequestHandler::class,
    ], 'pet_create');
    $web->get('/api/pets/{id}', [
        AcceptAndContentTypeMiddleware::class,
        ApiExceptionMiddleware::class,
        Pet::class.ReadRequestHandler::class,
    ], 'pet_read');
    $web->put('/api/pets/{id}', [
        AcceptAndContentTypeMiddleware::class,
        ApiExceptionMiddleware::class,
        Pet::class.UpdateRequestHandler::class,
    ], 'pet_update');
    $web->delete('/api/pets/{id}', [
        AcceptAndContentTypeMiddleware::class,
        ApiExceptionMiddleware::class,
        Pet::class.DeleteRequestHandler::class,
    ], 'pet_delete');

    return $web;
};
