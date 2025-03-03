<?php

declare(strict_types=1);

namespace App\Tests\Unit\ServiceFactory\Framework;

use App\ServiceFactory\Framework\UrlGeneratorFactory;
use Chubbyphp\Framework\Router\RoutesByNameInterface;
use Chubbyphp\Framework\Router\UrlGeneratorInterface;
use Chubbyphp\Mock\MockMethod\WithReturn;
use Chubbyphp\Mock\MockObjectBuilder;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;

/**
 * @covers \App\ServiceFactory\Framework\UrlGeneratorFactory
 *
 * @internal
 */
final class UrlGeneratorFactoryTest extends TestCase
{
    public function testInvoke(): void
    {
        $builder = new MockObjectBuilder();

        /** @var RoutesByNameInterface $routes */
        $routes = $builder->create(RoutesByNameInterface::class, [
            new WithReturn('getRoutesByName', [], []),
        ]);

        /** @var ContainerInterface $container */
        $container = $builder->create(ContainerInterface::class, [
            new WithReturn('get', [RoutesByNameInterface::class], $routes),
        ]);

        $factory = new UrlGeneratorFactory();

        self::assertInstanceOf(UrlGeneratorInterface::class, $factory($container));
    }
}
