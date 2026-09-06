<?php

declare(strict_types=1);

namespace App\Tests\Unit\Core\ServiceFactory\Framework;

use App\Core\ServiceFactory\Framework\RouteCollectorFactory;
use Chubbyphp\Mock\MockMethod\WithoutReturn;
use Chubbyphp\Mock\MockMethod\WithReturn;
use Chubbyphp\Mock\MockObjectBuilder;
use Mezzio\Router\Route;
use Mezzio\Router\RouteCollector;
use Mezzio\Router\RouterInterface;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;
use Psr\Http\Server\MiddlewareInterface;

/**
 * @covers \App\Core\ServiceFactory\Framework\RouteCollectorFactory
 *
 * @internal
 */
final class RouteCollectorFactoryTest extends TestCase
{
    public function testInvoke(): void
    {
        $builder = new MockObjectBuilder();

        /** @var MiddlewareInterface $dummyMiddleware */
        $dummyMiddleware = $builder->create(MiddlewareInterface::class, []);

        $routes = [
            new Route('/dummy1', $dummyMiddleware, ['GET'], 'dummy1'),
            new Route('/dummy2', $dummyMiddleware, ['POST'], 'dummy2'),
        ];

        /** @var RouterInterface $router */
        $router = $builder->create(RouterInterface::class, [
            new WithoutReturn('addRoute', [$routes[0]], false),
            new WithoutReturn('addRoute', [$routes[1]], false),
        ]);

        /** @var ContainerInterface $container */
        $container = $builder->create(ContainerInterface::class, [
            new WithReturn('get', [RouterInterface::class], $router),
            new WithReturn('get', [Route::class.'[]'], $routes),
        ]);

        $factory = new RouteCollectorFactory();

        $routeCollector = $factory($container);

        self::assertInstanceOf(RouteCollector::class, $routeCollector);
        self::assertEquals($routes, $routeCollector->getRoutes());
    }
}
