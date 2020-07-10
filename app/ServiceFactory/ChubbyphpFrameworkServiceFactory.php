<?php

declare(strict_types=1);

namespace App\ServiceFactory;

use App\Model\Pet;
use App\RequestHandler\Api\Crud\CreateRequestHandler;
use App\RequestHandler\Api\Crud\DeleteRequestHandler;
use App\RequestHandler\Api\Crud\ListRequestHandler;
use App\RequestHandler\Api\Crud\ReadRequestHandler;
use App\RequestHandler\Api\Crud\UpdateRequestHandler;
use App\RequestHandler\Api\PingRequestHandler;
use App\RequestHandler\Api\Swagger\IndexRequestHandler;
use App\RequestHandler\Api\Swagger\YamlRequestHandler;
use Chubbyphp\ApiHttp\Middleware\AcceptAndContentTypeMiddleware;
use Chubbyphp\ApiHttp\Middleware\ApiExceptionMiddleware;
use Chubbyphp\Framework\Middleware\ExceptionMiddleware;
use Chubbyphp\Framework\Middleware\LazyMiddleware;
use Chubbyphp\Framework\Middleware\RouterMiddleware;
use Chubbyphp\Framework\RequestHandler\LazyRequestHandler;
use Chubbyphp\Framework\Router\FastRoute\Router;
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
            RouterInterface::class.'routes' => static function (ContainerInterface $container) {
                $acceptAndContentType = new LazyMiddleware($container, AcceptAndContentTypeMiddleware::class);
                $apiException = new LazyMiddleware($container, ApiExceptionMiddleware::class);

                $ping = new LazyRequestHandler($container, PingRequestHandler::class);
                $index = new LazyRequestHandler($container, IndexRequestHandler::class);
                $yaml = new LazyRequestHandler($container, YamlRequestHandler::class);
                $petList = new LazyRequestHandler($container, ListRequestHandler::class.Pet::class);
                $petCreate = new LazyRequestHandler($container, CreateRequestHandler::class.Pet::class);
                $petRead = new LazyRequestHandler($container, ReadRequestHandler::class.Pet::class);
                $petUpdate = new LazyRequestHandler($container, UpdateRequestHandler::class.Pet::class);
                $petDelete = new LazyRequestHandler($container, DeleteRequestHandler::class.Pet::class);

                return Group::create('/api', [
                    Route::get('/ping', 'ping', $ping, [$acceptAndContentType, $apiException]),
                    Route::get('/swagger/index', 'swagger_index', $index),
                    Route::get('/swagger/yaml', 'swagger_yaml', $yaml),
                    Group::create('/pets', [
                        Route::get('', 'pet_list', $petList),
                        Route::post('', 'pet_create', $petCreate),
                        Route::get('/{id}', 'pet_read', $petRead),
                        Route::put('/{id}', 'pet_update', $petUpdate),
                        Route::delete('/{id}', 'pet_delete', $petDelete),
                    ], [$acceptAndContentType, $apiException]),
                ])->getRoutes();
            },
            RouterInterface::class => static function (ContainerInterface $container) {
                return new Router(
                    $container->get(RouterInterface::class.'routes'),
                    $container->get('routerCacheFile')
                );
            },
        ];
    }
}
