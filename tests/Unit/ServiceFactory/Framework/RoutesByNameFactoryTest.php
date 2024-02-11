<?php

declare(strict_types=1);

namespace App\Tests\Unit\ServiceFactory\Framework;

use App\Middleware\ApiExceptionMiddleware as MiddlewareApiExceptionMiddleware;
use App\Model\Pet;
use App\RequestHandler\Api\Crud\CreateRequestHandler;
use App\RequestHandler\Api\Crud\DeleteRequestHandler;
use App\RequestHandler\Api\Crud\ListRequestHandler;
use App\RequestHandler\Api\Crud\ReadRequestHandler;
use App\RequestHandler\Api\Crud\UpdateRequestHandler;
use App\RequestHandler\OpenapiRequestHandler;
use App\RequestHandler\PingRequestHandler;
use App\ServiceFactory\Framework\RoutesByNameFactory;
use Chubbyphp\Framework\Middleware\LazyMiddleware;
use Chubbyphp\Framework\RequestHandler\LazyRequestHandler;
use Chubbyphp\Framework\Router\Route;
use Chubbyphp\Mock\MockByCallsTrait;
use Chubbyphp\Negotiation\Middleware\AcceptMiddleware;
use Chubbyphp\Negotiation\Middleware\ContentTypeMiddleware;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;

/**
 * @covers \App\ServiceFactory\Framework\RoutesByNameFactory
 *
 * @internal
 */
final class RoutesByNameFactoryTest extends TestCase
{
    use MockByCallsTrait;

    public function testInvoke(): void
    {
        /** @var ContainerInterface $container */
        $container = $this->getMockByCalls(ContainerInterface::class);

        $ping = new LazyRequestHandler($container, PingRequestHandler::class);
        $openApi = new LazyRequestHandler($container, OpenapiRequestHandler::class);

        $accept = new LazyMiddleware($container, AcceptMiddleware::class);
        $contentType = new LazyMiddleware($container, ContentTypeMiddleware::class);
        $apiExceptionMiddleware = new LazyMiddleware($container, MiddlewareApiExceptionMiddleware::class);

        $petList = new LazyRequestHandler($container, Pet::class.ListRequestHandler::class);
        $petCreate = new LazyRequestHandler($container, Pet::class.CreateRequestHandler::class);
        $petRead = new LazyRequestHandler($container, Pet::class.ReadRequestHandler::class);
        $petUpdate = new LazyRequestHandler($container, Pet::class.UpdateRequestHandler::class);
        $petDelete = new LazyRequestHandler($container, Pet::class.DeleteRequestHandler::class);

        $factory = new RoutesByNameFactory();

        self::assertEquals(['ping' => Route::get('/ping', 'ping', $ping),
            'openapi' => Route::get('/openapi', 'openapi', $openApi),
            'pet_list' => Route::get('/api/pets', 'pet_list', $petList, [$accept, $apiExceptionMiddleware]),
            'pet_create' => Route::post('/api/pets', 'pet_create', $petCreate, [$accept, $apiExceptionMiddleware, $contentType]),
            'pet_read' => Route::get('/api/pets/{id}', 'pet_read', $petRead, [$accept, $apiExceptionMiddleware]),
            'pet_update' => Route::put('/api/pets/{id}', 'pet_update', $petUpdate, [$accept, $apiExceptionMiddleware, $contentType]),
            'pet_delete' => Route::delete('/api/pets/{id}', 'pet_delete', $petDelete, [$accept, $apiExceptionMiddleware]),
        ], $factory($container)->getRoutesByName());
    }
}
