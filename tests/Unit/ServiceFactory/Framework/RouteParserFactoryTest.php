<?php

declare(strict_types=1);

namespace App\Tests\Unit\ServiceFactory\Framework;

use App\ServiceFactory\Framework\RouteParserFactory;
use Chubbyphp\Mock\MockMethod\WithReturn;
use Chubbyphp\Mock\MockObjectBuilder;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;
use Slim\Interfaces\RouteCollectorInterface;
use Slim\Interfaces\RouteParserInterface;

/**
 * @covers \App\ServiceFactory\Framework\RouteParserFactory
 *
 * @internal
 */
final class RouteParserFactoryTest extends TestCase
{
    public function testInvoke(): void
    {
        $builder = new MockObjectBuilder();

        /** @var RouteParserInterface $routeParser */
        $routeParser = $builder->create(RouteParserInterface::class, []);

        /** @var RouteCollectorInterface $routeCollector */
        $routeCollector = $builder->create(RouteCollectorInterface::class, [
            new WithReturn('getRouteParser', [], $routeParser),
        ]);

        /** @var ContainerInterface $container */
        $container = $builder->create(ContainerInterface::class, [
            new WithReturn('get', [RouteCollectorInterface::class], $routeCollector),
        ]);

        $factory = new RouteParserFactory();

        self::assertInstanceOf(RouteParserInterface::class, $factory($container));
    }
}
