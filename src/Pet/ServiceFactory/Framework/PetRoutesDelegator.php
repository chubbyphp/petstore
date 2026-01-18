<?php

declare(strict_types=1);

namespace App\Pet\ServiceFactory\Framework;

use App\Pet\Model\Pet;
use Chubbyphp\Api\Middleware\ApiExceptionMiddleware;
use Chubbyphp\Api\RequestHandler\CreateRequestHandler;
use Chubbyphp\Api\RequestHandler\DeleteRequestHandler;
use Chubbyphp\Api\RequestHandler\ListRequestHandler;
use Chubbyphp\Api\RequestHandler\ReadRequestHandler;
use Chubbyphp\Api\RequestHandler\UpdateRequestHandler;
use Chubbyphp\Framework\Middleware\LazyMiddleware;
use Chubbyphp\Framework\RequestHandler\LazyRequestHandler;
use Chubbyphp\Framework\Router\Group;
use Chubbyphp\Framework\Router\Route;
use Chubbyphp\Framework\Router\RouteInterface;
use Chubbyphp\Negotiation\Middleware\AcceptMiddleware;
use Chubbyphp\Negotiation\Middleware\ContentTypeMiddleware;
use Psr\Container\ContainerInterface;

final class PetRoutesDelegator
{
    /**
     * @return array<RouteInterface>
     */
    public function __invoke(ContainerInterface $container, mixed $_, callable $callback): array
    {
        /** @var array<RouteInterface> $routes */
        $routes = $callback();

        $accept = new LazyMiddleware($container, AcceptMiddleware::class);
        $contentType = new LazyMiddleware($container, ContentTypeMiddleware::class);
        $apiExceptionMiddleware = new LazyMiddleware($container, ApiExceptionMiddleware::class);

        $petList = new LazyRequestHandler($container, Pet::class.ListRequestHandler::class);
        $petCreate = new LazyRequestHandler($container, Pet::class.CreateRequestHandler::class);
        $petRead = new LazyRequestHandler($container, Pet::class.ReadRequestHandler::class);
        $petUpdate = new LazyRequestHandler($container, Pet::class.UpdateRequestHandler::class);
        $petDelete = new LazyRequestHandler($container, Pet::class.DeleteRequestHandler::class);

        return [
            ...$routes,
            ...Group::create('', [
                Group::create('/api', [
                    Group::create('/pets', [
                        Route::get('', 'pet_list', $petList),
                        Route::post('', 'pet_create', $petCreate, [$contentType]),
                        Route::get('/{id}', 'pet_read', $petRead),
                        Route::put('/{id}', 'pet_update', $petUpdate, [$contentType]),
                        Route::delete('/{id}', 'pet_delete', $petDelete),
                    ]),
                ], [$accept, $apiExceptionMiddleware]),
            ])->getRoutes(),
        ];
    }
}
