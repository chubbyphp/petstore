<?php

declare(strict_types=1);

namespace App\Tests\Unit\ServiceFactory\Framework;

use App\ServiceFactory\Framework\RouteMatcherMiddlewareFactory;
use Chubbyphp\Framework\Middleware\RouteMatcherMiddleware;
use Chubbyphp\Framework\Router\RouteMatcherInterface;
use Chubbyphp\Mock\MockMethod\WithReturn;
use Chubbyphp\Mock\MockObjectBuilder;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;

/**
 * @covers \App\ServiceFactory\Framework\RouteMatcherMiddlewareFactory
 *
 * @internal
 */
final class RouteMatcherMiddlewareFactoryTest extends TestCase
{
    public function testInvoke(): void
    {
        $builder = new MockObjectBuilder();

        /** @var RouteMatcherInterface $routeMatcher */
        $routeMatcher = $builder->create(RouteMatcherInterface::class, []);

        /** @var ContainerInterface $container */
        $container = $builder->create(ContainerInterface::class, [
            new WithReturn('get', [RouteMatcherInterface::class], $routeMatcher),
        ]);

        $factory = new RouteMatcherMiddlewareFactory();

        self::assertInstanceOf(RouteMatcherMiddleware::class, $factory($container));
    }
}
