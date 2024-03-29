<?php

declare(strict_types=1);

namespace App\ServiceFactory\Framework;

use App\Middleware\ApiExceptionMiddleware;
use App\Model\Pet;
use App\RequestHandler\Api\Crud\CreateRequestHandler;
use App\RequestHandler\Api\Crud\DeleteRequestHandler;
use App\RequestHandler\Api\Crud\ListRequestHandler;
use App\RequestHandler\Api\Crud\ReadRequestHandler;
use App\RequestHandler\Api\Crud\UpdateRequestHandler;
use App\RequestHandler\OpenapiRequestHandler;
use App\RequestHandler\PingRequestHandler;
use Chubbyphp\Framework\Middleware\LazyMiddleware;
use Chubbyphp\Framework\RequestHandler\LazyRequestHandler;
use Chubbyphp\Framework\Router\Group;
use Chubbyphp\Framework\Router\Route;
use Chubbyphp\Framework\Router\RoutesByName;
use Chubbyphp\Framework\Router\RoutesByNameInterface;
use Chubbyphp\Negotiation\Middleware\AcceptMiddleware;
use Chubbyphp\Negotiation\Middleware\ContentTypeMiddleware;
use Psr\Container\ContainerInterface;

final class RoutesByNameFactory
{
    public function __invoke(ContainerInterface $container): RoutesByNameInterface
    {
        $ping = new LazyRequestHandler($container, PingRequestHandler::class);
        $openApi = new LazyRequestHandler($container, OpenapiRequestHandler::class);

        $accept = new LazyMiddleware($container, AcceptMiddleware::class);
        $contentType = new LazyMiddleware($container, ContentTypeMiddleware::class);
        $apiExceptionMiddleware = new LazyMiddleware($container, ApiExceptionMiddleware::class);

        $petList = new LazyRequestHandler($container, Pet::class.ListRequestHandler::class);
        $petCreate = new LazyRequestHandler($container, Pet::class.CreateRequestHandler::class);
        $petRead = new LazyRequestHandler($container, Pet::class.ReadRequestHandler::class);
        $petUpdate = new LazyRequestHandler($container, Pet::class.UpdateRequestHandler::class);
        $petDelete = new LazyRequestHandler($container, Pet::class.DeleteRequestHandler::class);

        return new RoutesByName(
            Group::create('', [
                Route::get('/ping', 'ping', $ping),
                Route::get('/openapi', 'openapi', $openApi),
                Group::create('/api', [
                    Group::create('/pets', [
                        Route::get('', 'pet_list', $petList),
                        Route::post('', 'pet_create', $petCreate, [$contentType]),
                        Route::get('/{id}', 'pet_read', $petRead),
                        Route::put('/{id}', 'pet_update', $petUpdate, [$contentType]),
                        Route::delete('/{id}', 'pet_delete', $petDelete),
                    ]),
                ], [$accept, $apiExceptionMiddleware]),
            ])->getRoutes()
        );
    }
}
