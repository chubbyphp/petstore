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

        $acceptAndContentType = new LazyMiddleware($container, AcceptAndContentTypeMiddleware::class);
        $apiExceptionMiddleware = new LazyMiddleware($container, ApiExceptionMiddleware::class);

        $ping = new LazyRequestHandler($container, PingRequestHandler::class);
        $index = new LazyRequestHandler($container, IndexRequestHandler::class);
        $yaml = new LazyRequestHandler($container, YamlRequestHandler::class);
        $petList = new LazyRequestHandler($container, Pet::class.ListRequestHandler::class);
        $petCreate = new LazyRequestHandler($container, Pet::class.CreateRequestHandler::class);
        $petRead = new LazyRequestHandler($container, Pet::class.ReadRequestHandler::class);
        $petUpdate = new LazyRequestHandler($container, Pet::class.UpdateRequestHandler::class);
        $petDelete = new LazyRequestHandler($container, Pet::class.DeleteRequestHandler::class);

        $factory = new RoutesFactory();

        self::assertEquals([
            'ping' => Route::get('/api/ping', 'ping', $ping, [$apiExceptionMiddleware, $acceptAndContentType]),
            'swagger_index' => Route::get('/api/swagger/index', 'swagger_index', $index),
            'swagger_yaml' => Route::get('/api/swagger/yaml', 'swagger_yaml', $yaml),
            'pet_list' => Route::get('/api/pets', 'pet_list', $petList, [$apiExceptionMiddleware, $acceptAndContentType]),
            'pet_create' => Route::post('/api/pets', 'pet_create', $petCreate, [$apiExceptionMiddleware, $acceptAndContentType]),
            'pet_read' => Route::get('/api/pets/{id}', 'pet_read', $petRead, [$apiExceptionMiddleware, $acceptAndContentType]),
            'pet_update' => Route::put('/api/pets/{id}', 'pet_update', $petUpdate, [$apiExceptionMiddleware, $acceptAndContentType]),
            'pet_delete' => Route::delete('/api/pets/{id}', 'pet_delete', $petDelete, [$apiExceptionMiddleware, $acceptAndContentType]),
        ], $factory($container)->getRoutesByName());
    }
}
