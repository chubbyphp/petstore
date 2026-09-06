<?php

declare(strict_types=1);

namespace App\Tests\Unit\Core\ServiceFactory\Framework;

use App\Core\RequestHandler\OpenapiRequestHandler;
use App\Core\RequestHandler\PingRequestHandler;
use App\Core\ServiceFactory\Framework\RoutesDelegator;
use Chubbyphp\Mock\MockMethod\WithReturn;
use Chubbyphp\Mock\MockObjectBuilder;
use Mezzio\MiddlewareContainer;
use Mezzio\MiddlewareFactory;
use Mezzio\MiddlewareFactoryInterface;
use Mezzio\Router\Route;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;
use Psr\Http\Server\MiddlewareInterface;

/**
 * @covers \App\Core\ServiceFactory\Framework\RoutesDelegator
 *
 * @internal
 */
final class RoutesDelegatorTest extends TestCase
{
    public function testInvoke(): void
    {
        $builder = new MockObjectBuilder();

        /** @var MiddlewareInterface $dummyMiddleware */
        $dummyMiddleware = $builder->create(MiddlewareInterface::class, []);

        /** @var ContainerInterface $middlewareContainerContainer */
        $middlewareContainerContainer = $builder->create(ContainerInterface::class, []);

        $middlewareFactory = new MiddlewareFactory(new MiddlewareContainer($middlewareContainerContainer));

        /** @var ContainerInterface $container */
        $container = $builder->create(ContainerInterface::class, [
            new WithReturn('get', [MiddlewareFactoryInterface::class], $middlewareFactory),
        ]);

        $ping = $middlewareFactory->lazy(PingRequestHandler::class);
        $openApi = $middlewareFactory->lazy(OpenapiRequestHandler::class);

        $factory = new RoutesDelegator();

        self::assertEquals([
            new Route('/dummy1', $dummyMiddleware, ['GET'], 'dummy1'),
            new Route('/dummy2', $dummyMiddleware, ['GET'], 'dummy2'),
            new Route('/ping', $ping, ['GET'], 'ping'),
            new Route('/openapi', $openApi, ['GET'], 'openapi'),
        ], $factory($container, '', static fn () => [
            new Route('/dummy1', $dummyMiddleware, ['GET'], 'dummy1'),
            new Route('/dummy2', $dummyMiddleware, ['GET'], 'dummy2'),
        ]));
    }
}
