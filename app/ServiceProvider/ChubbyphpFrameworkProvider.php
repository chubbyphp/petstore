<?php

declare(strict_types=1);

namespace App\ServiceProvider;

use App\Controller\Crud\CreateController;
use App\Controller\Crud\DeleteController;
use App\Controller\Crud\ListController;
use App\Controller\Crud\ReadController;
use App\Controller\Crud\UpdateController;
use App\Controller\IndexController;
use App\Controller\PingController;
use App\Controller\Swagger\IndexController as SwaggerIndexController;
use App\Controller\Swagger\YamlController as SwaggerYamlController;
use App\Model\Pet;
use Chubbyphp\ApiHttp\Middleware\AcceptAndContentTypeMiddleware;
use Chubbyphp\Framework\ExceptionHandler;
use Chubbyphp\Framework\Middleware\LazyMiddleware;
use Chubbyphp\Framework\Middleware\MiddlewareDispatcher;
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
        $container[ExceptionHandler::class] = function () use ($container) {
            return new ExceptionHandler($container['api-http.response.factory'], $container['debug']);
        };

        $container[FastRouteRouter::class] = function () use ($container) {
            return new FastRouteRouter($container['routes'], $container['routerCacheFile']);
        };

        $container[MiddlewareDispatcher::class] = function () {
            return new MiddlewareDispatcher();
        };

        $container['routes'] = function () use ($container) {
            $psrContainer = new PsrContainer($container);

            $acceptAndContentTypeMiddleware = new LazyMiddleware($psrContainer, AcceptAndContentTypeMiddleware::class);

            $indexController = new LazyRequestHandler($psrContainer, IndexController::class);
            $swaggerIndexController = new LazyRequestHandler($psrContainer, SwaggerIndexController::class);
            $swaggerYamlController = new LazyRequestHandler($psrContainer, SwaggerYamlController::class);
            $pingController = new LazyRequestHandler($psrContainer, PingController::class);
            $petListController = new LazyRequestHandler($psrContainer, ListController::class.Pet::class);
            $petCreateController = new LazyRequestHandler($psrContainer, CreateController::class.Pet::class);
            $petReadController = new LazyRequestHandler($psrContainer, ReadController::class.Pet::class);
            $petUpdateController = new LazyRequestHandler($psrContainer, UpdateController::class.Pet::class);
            $petDeleteController = new LazyRequestHandler($psrContainer, DeleteController::class.Pet::class);

            return Group::create('')
                ->route(Route::get('/', 'index', $indexController))
                ->group(Group::create('/api')
                    ->route(Route::get('', 'swagger_index', $swaggerIndexController))
                    ->route(Route::get('/swagger', 'swagger_yml', $swaggerYamlController))
                    ->route(Route::get('/ping', 'ping', $pingController)
                        ->middleware($acceptAndContentTypeMiddleware)
                    )
                    ->group(Group::create('/pets')
                        ->route(Route::get('', 'pet_list', $petListController))
                        ->route(Route::post('', 'pet_create', $petCreateController))
                        ->route(Route::get('/{id}', 'pet_read', $petReadController))
                        ->route(Route::put('/{id}', 'pet_update', $petUpdateController))
                        ->route(Route::delete('/{id}', 'pet_delete', $petDeleteController))
                        ->middleware($acceptAndContentTypeMiddleware)
                    )
                )
                ->getRoutes();
        };
    }
}
