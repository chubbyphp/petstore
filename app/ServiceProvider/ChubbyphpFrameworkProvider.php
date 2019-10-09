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
    public function register(Container $container): void
    {
        $container[ExceptionMiddleware::class] = static function () use ($container) {
            return new ExceptionMiddleware(
                $container['api-http.response.factory'],
                $container['debug'],
                $container['logger']
            );
        };

        $container[RouterMiddleware::class] = static function () use ($container) {
            return new RouterMiddleware($container[FastRouteRouter::class], $container['api-http.response.factory']);
        };

        $container[FastRouteRouter::class] = static function () use ($container) {
            return new FastRouteRouter($container['routes'], $container['routerCacheFile']);
        };

        $container['routes'] = static function () use ($container) {
            $acceptAndContentTypeMiddleware = new LazyMiddleware(
                $container[PsrContainer::class],
                AcceptAndContentTypeMiddleware::class
            );
            $indexRequestHandler = new LazyRequestHandler($container[PsrContainer::class], IndexRequestHandler::class);
            $swaggerIndexRequestHandler = new LazyRequestHandler(
                $container[PsrContainer::class],
                SwaggerIndexRequestHandler::class
            );
            $swaggerYamlRequestHandler = new LazyRequestHandler(
                $container[PsrContainer::class],
                SwaggerYamlRequestHandler::class
            );
            $pingRequestHandler = new LazyRequestHandler(
                $container[PsrContainer::class],
                PingRequestHandler::class
            );
            $petListRequestHandler = new LazyRequestHandler(
                $container[PsrContainer::class],
                ListRequestHandler::class.Pet::class
            );
            $petCreateRequestHandler = new LazyRequestHandler(
                $container[PsrContainer::class],
                CreateRequestHandler::class.Pet::class
            );
            $petReadRequestHandler = new LazyRequestHandler(
                $container[PsrContainer::class],
                ReadRequestHandler::class.Pet::class
            );
            $petUpdateRequestHandler = new LazyRequestHandler(
                $container[PsrContainer::class],
                UpdateRequestHandler::class.Pet::class
            );
            $petDeleteRequestHandler = new LazyRequestHandler(
                $container[PsrContainer::class],
                DeleteRequestHandler::class.Pet::class
            );

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
