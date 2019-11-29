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
                    $container->get(FastRouteRouter::class),
                    $container->get('api-http.response.factory')
                );
            },
            FastRouteRouter::class => static function (ContainerInterface $container) {
                return new FastRouteRouter($container->get('routes'), $container->get('routerCacheFile'));
            },
            'routes' => static function (ContainerInterface $container) {
                $acceptAndContentTypeMiddleware = new LazyMiddleware(
                    $container,
                    AcceptAndContentTypeMiddleware::class
                );
                $indexRequestHandler = new LazyRequestHandler(
                    $container,
                    IndexRequestHandler::class
                );
                $swaggerIndexRequestHandler = new LazyRequestHandler(
                    $container,
                    SwaggerIndexRequestHandler::class
                );
                $swaggerYamlRequestHandler = new LazyRequestHandler(
                    $container,
                    SwaggerYamlRequestHandler::class
                );
                $pingRequestHandler = new LazyRequestHandler(
                    $container,
                    PingRequestHandler::class
                );
                $petListRequestHandler = new LazyRequestHandler(
                    $container,
                    ListRequestHandler::class.Pet::class
                );
                $petCreateRequestHandler = new LazyRequestHandler(
                    $container,
                    CreateRequestHandler::class.Pet::class
                );
                $petReadRequestHandler = new LazyRequestHandler(
                    $container,
                    ReadRequestHandler::class.Pet::class
                );
                $petUpdateRequestHandler = new LazyRequestHandler(
                    $container,
                    UpdateRequestHandler::class.Pet::class
                );
                $petDeleteRequestHandler = new LazyRequestHandler(
                    $container,
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
            },
        ];
    }
}
