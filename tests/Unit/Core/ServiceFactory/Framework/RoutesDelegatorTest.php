<?php

declare(strict_types=1);

namespace App\Tests\Unit\Core\ServiceFactory\Framework;

use App\Core\RequestHandler\OpenapiRequestHandler;
use App\Core\RequestHandler\PingRequestHandler;
use App\Core\ServiceFactory\Framework\RoutesDelegator;
use Chubbyphp\Framework\RequestHandler\LazyRequestHandler;
use Chubbyphp\Framework\Router\Route;
use Chubbyphp\Mock\MockObjectBuilder;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;
use Psr\Http\Server\RequestHandlerInterface;

/**
 * @covers \App\Core\ServiceFactory\Framework\PetRoutesDelegator
 *
 * @internal
 */
final class RoutesDelegatorTest extends TestCase
{
    public function testInvoke(): void
    {
        $builder = new MockObjectBuilder();

        /** @var RequestHandlerInterface $dummyHandler */
        $dummyHandler = $builder->create(RequestHandlerInterface::class, []);

        /** @var ContainerInterface $container */
        $container = $builder->create(ContainerInterface::class, []);

        $ping = new LazyRequestHandler($container, PingRequestHandler::class);
        $openApi = new LazyRequestHandler($container, OpenapiRequestHandler::class);

        $factory = new RoutesDelegator();

        self::assertEquals([
            Route::get('/dummy1', 'dummy1', $dummyHandler, []),
            Route::get('/dummy2', 'dummy2', $dummyHandler, []),
            Route::get('/ping', 'ping', $ping),
            Route::get('/openapi', 'openapi', $openApi),
        ], $factory($container, '', static fn () => [
            Route::get('/dummy1', 'dummy1', $dummyHandler, []),
            Route::get('/dummy2', 'dummy2', $dummyHandler, []),
        ]));
    }
}
