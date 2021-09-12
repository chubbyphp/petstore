<?php

declare(strict_types=1);

namespace App\Tests\Unit\ServiceFactory\Framework;

use App\Model\Pet;
use App\RequestHandler\Api\Crud\CreateRequestHandler;
use App\RequestHandler\Api\Crud\DeleteRequestHandler;
use App\RequestHandler\Api\Crud\ListRequestHandler;
use App\RequestHandler\Api\Crud\ReadRequestHandler;
use App\RequestHandler\Api\Crud\UpdateRequestHandler;
use App\RequestHandler\Api\PingRequestHandler;
use App\RequestHandler\Api\Swagger\IndexRequestHandler;
use App\RequestHandler\Api\Swagger\YamlRequestHandler;
use App\ServiceFactory\Framework\RoutesFactory;
use Chubbyphp\ApiHttp\Middleware\AcceptAndContentTypeMiddleware;
use Chubbyphp\ApiHttp\Middleware\ApiExceptionMiddleware;
use Chubbyphp\Framework\Middleware\LazyMiddleware;
use Chubbyphp\Framework\RequestHandler\LazyRequestHandler;
use Chubbyphp\Framework\Router\Route;
use Chubbyphp\Mock\MockByCallsTrait;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;

/**
 * @covers \App\ServiceFactory\Framework\RoutesFactory
 *
 * @internal
 */
final class RoutesFactoryTest extends TestCase
{
    use MockByCallsTrait;

    public function testInvoke(): void
    {
        /** @var ContainerInterface $container */
        $container = $this->getMockByCalls(ContainerInterface::class);

        $m = static fn (string $name) => new LazyMiddleware($container, $name);
        $r = static fn (string $name) => new LazyRequestHandler($container, $name);

        $factory = new RoutesFactory();

        self::assertEquals([
            'ping' => Route::get('/api/ping', 'ping', $r(PingRequestHandler::class), [
                $m(ApiExceptionMiddleware::class),
                $m(AcceptAndContentTypeMiddleware::class),
            ]),
            'swagger_index' => Route::get('/api/swagger/index', 'swagger_index', $r(IndexRequestHandler::class)),
            'swagger_yaml' => Route::get('/api/swagger/yaml', 'swagger_yaml', $r(YamlRequestHandler::class)),
            'pet_list' => Route::get('/api/pets', 'pet_list', $r(Pet::class.ListRequestHandler::class), [
                $m(ApiExceptionMiddleware::class),
                $m(AcceptAndContentTypeMiddleware::class),
            ]),
            'pet_create' => Route::post('/api/pets', 'pet_create', $r(Pet::class.CreateRequestHandler::class), [
                $m(ApiExceptionMiddleware::class),
                $m(AcceptAndContentTypeMiddleware::class),
            ]),
            'pet_read' => Route::get('/api/pets/{id}', 'pet_read', $r(Pet::class.ReadRequestHandler::class), [
                $m(ApiExceptionMiddleware::class),
                $m(AcceptAndContentTypeMiddleware::class),
            ]),
            'pet_update' => Route::put('/api/pets/{id}', 'pet_update', $r(Pet::class.UpdateRequestHandler::class), [
                $m(ApiExceptionMiddleware::class),
                $m(AcceptAndContentTypeMiddleware::class),
            ]),
            'pet_delete' => Route::delete('/api/pets/{id}', 'pet_delete', $r(Pet::class.DeleteRequestHandler::class), [
                $m(ApiExceptionMiddleware::class),
                $m(AcceptAndContentTypeMiddleware::class),
            ]),
        ], $factory($container)->getRoutesByName());
    }
}
