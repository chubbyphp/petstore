<?php

declare(strict_types=1);

namespace App\ServiceProvider;

use App\Model\Pet;
use App\RequestHandler\Crud\CreateRequestHandler;
use App\RequestHandler\Crud\DeleteRequestHandler;
use App\RequestHandler\Crud\ListRequestHandler;
use App\RequestHandler\Crud\ReadRequestHandler;
use App\RequestHandler\Crud\UpdateRequestHandler;
use App\RequestHandler\IndexRequestHandler;
use App\RequestHandler\PingRequestHandler;
use App\RequestHandler\Swagger\IndexRequestHandler as SwaggerIndexRequestHandler;
use App\RequestHandler\Swagger\YamlRequestHandler as SwaggerYamlRequestHandler;
use Chubbyphp\ApiHttp\Middleware\AcceptAndContentTypeMiddleware;
use Chubbyphp\Framework\Middleware\ExceptionMiddleware;
use Chubbyphp\Framework\Middleware\LazyMiddleware;
use Chubbyphp\Framework\Middleware\RouterMiddleware;
use Chubbyphp\Framework\RequestHandler\LazyRequestHandler;
use Chubbyphp\Framework\Router\FastRouteRouter;
use Chubbyphp\Framework\Router\Group;
use Chubbyphp\Framework\Router\Route;
use Pimple\Container;
use Pimple\Psr11\Container as PsrContainer;
use Pimple\ServiceProviderInterface;

final class ChubbyphpFrameworkProvider implements ServiceProviderInterface
{
    /**
     * @param Container $container
     */
    public function register(Container $container): void
    {
        $container[ExceptionMiddleware::class] = function () use ($container) {
            return new ExceptionMiddleware(
                $container['api-http.response.factory'],
                $container['debug'],
                $container['logger']
            );
        };

        $container[RouterMiddleware::class] = function () use ($container) {
            return new RouterMiddleware($container[FastRouteRouter::class], $container['api-http.response.factory']);
        };

        $container[FastRouteRouter::class] = function () use ($container) {
            return new FastRouteRouter($container['routes'], $container['routerCacheFile']);
        };

        $container['routes'] = function () use ($container) {
            $psrContainer = new PsrContainer($container);

            $acceptAndContentTypeMiddleware = new LazyMiddleware($psrContainer, AcceptAndContentTypeMiddleware::class);

            $indexRequestHandler = new LazyRequestHandler($psrContainer, IndexRequestHandler::class);
            $swaggerIndexRequestHandler = new LazyRequestHandler($psrContainer, SwaggerIndexRequestHandler::class);
            $swaggerYamlRequestHandler = new LazyRequestHandler($psrContainer, SwaggerYamlRequestHandler::class);
            $pingRequestHandler = new LazyRequestHandler($psrContainer, PingRequestHandler::class);
            $petListRequestHandler = new LazyRequestHandler($psrContainer, ListRequestHandler::class.Pet::class);
            $petCreateRequestHandler = new LazyRequestHandler($psrContainer, CreateRequestHandler::class.Pet::class);
            $petReadRequestHandler = new LazyRequestHandler($psrContainer, ReadRequestHandler::class.Pet::class);
            $petUpdateRequestHandler = new LazyRequestHandler($psrContainer, UpdateRequestHandler::class.Pet::class);
            $petDeleteRequestHandler = new LazyRequestHandler($psrContainer, DeleteRequestHandler::class.Pet::class);

            return Group::create('')
                ->route(Route::get('/', 'index', $indexRequestHandler))
                ->group(Group::create('/api')
                    ->route(Route::get('', 'swagger_index', $swaggerIndexRequestHandler))
                    ->route(Route::get('/swagger', 'swagger_yml', $swaggerYamlRequestHandler))
                    ->route(Route::get('/ping', 'ping', $pingRequestHandler)
                        ->middleware($acceptAndContentTypeMiddleware)
                    )
                    ->group(Group::create('/pets')
                        ->route(Route::get('', 'pet_list', $petListRequestHandler))
                        ->route(Route::post('', 'pet_create', $petCreateRequestHandler))
                        ->route(Route::get('/{id}', 'pet_read', $petReadRequestHandler))
                        ->route(Route::put('/{id}', 'pet_update', $petUpdateRequestHandler))
                        ->route(Route::delete('/{id}', 'pet_delete', $petDeleteRequestHandler))
                        ->middleware($acceptAndContentTypeMiddleware)
                    )
                )
                ->getRoutes()
            ;
        };
    }
}
