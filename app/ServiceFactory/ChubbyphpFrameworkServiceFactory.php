<?php

declare(strict_types=1);

namespace App\ServiceFactory;

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
use Chubbyphp\Framework\Router\RouterInterface;
use Psr\Container\ContainerInterface;

final class ChubbyphpFrameworkServiceFactory
{
    /**
     * @return array<string, callable>
     */
    public function __invoke(): array
    {
        return [
            ExceptionMiddleware::class => static function (ContainerInterface $container) {
                return new ExceptionMiddleware(
                    $container->get('api-http.response.factory'),
                    $container->get('debug'),
                    $container->get('logger')
                );
            },
            RouterMiddleware::class => static function (ContainerInterface $container) {
                return new RouterMiddleware(
                    $container->get(RouterInterface::class),
                    $container->get('api-http.response.factory')
                );
            },
            RouterInterface::class => static function (ContainerInterface $container) {
                $acceptAndContentType = new LazyMiddleware($container, AcceptAndContentTypeMiddleware::class);

                $index = new LazyRequestHandler($container, IndexRequestHandler::class);
                $swaggerIndex = new LazyRequestHandler($container, SwaggerIndexRequestHandler::class);
                $swaggerYaml = new LazyRequestHandler($container, SwaggerYamlRequestHandler::class);
                $ping = new LazyRequestHandler($container, PingRequestHandler::class);
                $petList = new LazyRequestHandler($container, ListRequestHandler::class.Pet::class);
                $petCreate = new LazyRequestHandler($container, CreateRequestHandler::class.Pet::class);
                $petRead = new LazyRequestHandler($container, ReadRequestHandler::class.Pet::class);
                $petUpdate = new LazyRequestHandler($container, UpdateRequestHandler::class.Pet::class);
                $petDelete = new LazyRequestHandler($container, DeleteRequestHandler::class.Pet::class);

                return new FastRouteRouter(
                    Group::create('')
                        ->route(Route::get('/', 'index', $index))
                        ->group(
                            Group::create('/api')
                                ->route(Route::get('', 'swagger_index', $swaggerIndex))
                                ->route(Route::get('/swagger', 'swagger_yml', $swaggerYaml))
                                ->route(Route::get('/ping', 'ping', $ping)->middleware($acceptAndContentType))
                                ->group(
                                    Group::create('/pets')
                                        ->route(Route::get('', 'pet_list', $petList))
                                        ->route(Route::post('', 'pet_create', $petCreate))
                                        ->route(Route::get('/{id}', 'pet_read', $petRead))
                                        ->route(Route::put('/{id}', 'pet_update', $petUpdate))
                                        ->route(Route::delete('/{id}', 'pet_delete', $petDelete))
                                        ->middleware($acceptAndContentType)
                                )
                        )
                        ->getRoutes(),
                    $container->get('routerCacheFile'));
            },
        ];
    }
}
