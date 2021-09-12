<?php

declare(strict_types=1);

namespace App\ServiceFactory\Framework;

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
use Chubbyphp\Framework\Middleware\LazyMiddleware;
use Chubbyphp\Framework\RequestHandler\LazyRequestHandler;
use Chubbyphp\Framework\Router\Group;
use Chubbyphp\Framework\Router\Route;
use Chubbyphp\Framework\Router\Routes;
use Psr\Container\ContainerInterface;

final class RoutesFactory
{
    public function __invoke(ContainerInterface $container): Routes
    {
        $m = static fn (string $name) => new LazyMiddleware($container, $name);
        $r = static fn (string $name) => new LazyRequestHandler($container, $name);

        return new Routes(
            Group::create('', [
                Group::create('/api', [
                    Route::get('/ping', 'ping', $r(PingRequestHandler::class), [
                        $m(ApiExceptionMiddleware::class),
                        $m(AcceptAndContentTypeMiddleware::class),
                    ]),
                    Route::get('/swagger/index', 'swagger_index', $r(IndexRequestHandler::class)),
                    Route::get('/swagger/yaml', 'swagger_yaml', $r(YamlRequestHandler::class)),
                    Group::create('/pets', [
                        Route::get('', 'pet_list', $r(Pet::class.ListRequestHandler::class)),
                        Route::post('', 'pet_create', $r(Pet::class.CreateRequestHandler::class)),
                        Route::get('/{id}', 'pet_read', $r(Pet::class.ReadRequestHandler::class)),
                        Route::put('/{id}', 'pet_update', $r(Pet::class.UpdateRequestHandler::class)),
                        Route::delete('/{id}', 'pet_delete', $r(Pet::class.DeleteRequestHandler::class)),
                    ], [$m(ApiExceptionMiddleware::class), $m(AcceptAndContentTypeMiddleware::class)]),
                ]),
            ])->getRoutes()
        );
    }
}
