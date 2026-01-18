<?php

declare(strict_types=1);

namespace App\Tests\Unit\Pet\ServiceFactory\Framework;

use App\Core\Middleware\ApiExceptionMiddleware as MiddlewareApiExceptionMiddleware;
use App\Core\RequestHandler\Api\Crud\CreateRequestHandler;
use App\Core\RequestHandler\Api\Crud\DeleteRequestHandler;
use App\Core\RequestHandler\Api\Crud\ListRequestHandler;
use App\Core\RequestHandler\Api\Crud\ReadRequestHandler;
use App\Core\RequestHandler\Api\Crud\UpdateRequestHandler;
use App\Pet\Model\Pet;
use App\Pet\ServiceFactory\Framework\PetRoutesDelegator;
use Chubbyphp\Framework\Middleware\LazyMiddleware;
use Chubbyphp\Framework\RequestHandler\LazyRequestHandler;
use Chubbyphp\Framework\Router\Route;
use Chubbyphp\Mock\MockObjectBuilder;
use Chubbyphp\Negotiation\Middleware\AcceptMiddleware;
use Chubbyphp\Negotiation\Middleware\ContentTypeMiddleware;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;
use Psr\Http\Server\RequestHandlerInterface;

/**
 * @covers \App\Pet\ServiceFactory\Framework\PetRoutesDelegator
 *
 * @internal
 */
final class PetRoutesDelegatorTest extends TestCase
{
    public function testInvoke(): void
    {
        $builder = new MockObjectBuilder();

        /** @var RequestHandlerInterface $dummyHandler */
        $dummyHandler = $builder->create(RequestHandlerInterface::class, []);

        /** @var ContainerInterface $container */
        $container = $builder->create(ContainerInterface::class, []);

        $accept = new LazyMiddleware($container, AcceptMiddleware::class);
        $contentType = new LazyMiddleware($container, ContentTypeMiddleware::class);
        $apiExceptionMiddleware = new LazyMiddleware($container, MiddlewareApiExceptionMiddleware::class);

        $petList = new LazyRequestHandler($container, Pet::class.ListRequestHandler::class);
        $petCreate = new LazyRequestHandler($container, Pet::class.CreateRequestHandler::class);
        $petRead = new LazyRequestHandler($container, Pet::class.ReadRequestHandler::class);
        $petUpdate = new LazyRequestHandler($container, Pet::class.UpdateRequestHandler::class);
        $petDelete = new LazyRequestHandler($container, Pet::class.DeleteRequestHandler::class);

        $factory = new PetRoutesDelegator();

        self::assertEquals([
            Route::get('/dummy1', 'dummy1', $dummyHandler, []),
            Route::get('/dummy2', 'dummy2', $dummyHandler, []),
            Route::get('/api/pets', 'pet_list', $petList, [$accept, $apiExceptionMiddleware]),
            Route::post('/api/pets', 'pet_create', $petCreate, [$accept, $apiExceptionMiddleware, $contentType]),
            Route::get('/api/pets/{id}', 'pet_read', $petRead, [$accept, $apiExceptionMiddleware]),
            Route::put('/api/pets/{id}', 'pet_update', $petUpdate, [$accept, $apiExceptionMiddleware, $contentType]),
            Route::delete('/api/pets/{id}', 'pet_delete', $petDelete, [$accept, $apiExceptionMiddleware]),
        ], $factory($container, '', static fn () => [
            Route::get('/dummy1', 'dummy1', $dummyHandler, []),
            Route::get('/dummy2', 'dummy2', $dummyHandler, []),
        ]));
    }
}
