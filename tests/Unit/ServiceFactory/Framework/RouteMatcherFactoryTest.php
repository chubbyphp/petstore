<?php

declare(strict_types=1);

namespace App\Tests\Unit\ServiceFactory\Framework;

use App\ServiceFactory\Framework\RouteMatcherFactory;
use Chubbyphp\Framework\Router\RouteMatcherInterface;
use Chubbyphp\Framework\Router\RoutesByNameInterface;
use Chubbyphp\Mock\MockMethod\WithReturn;
use Chubbyphp\Mock\MockObjectBuilder;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;

/**
 * @covers \App\ServiceFactory\Framework\RouteMatcherFactory
 *
 * @internal
 */
final class RouteMatcherFactoryTest extends TestCase
{
    public function testInvoke(): void
    {
        $config = [
            'fastroute' => [
                'cache' => sys_get_temp_dir().'/'.uniqid('fastroute-cache-').'.php',
            ],
        ];

        $builder = new MockObjectBuilder();

        /** @var RoutesByNameInterface $routes */
        $routes = $builder->create(RoutesByNameInterface::class, [
            new WithReturn('getRoutesByName', [], []),
        ]);

        /** @var ContainerInterface $container */
        $container = $builder->create(ContainerInterface::class, [
            new WithReturn('get', [RoutesByNameInterface::class], $routes),
            new WithReturn('get', ['config'], $config),
        ]);

        $factory = new RouteMatcherFactory();

        self::assertInstanceOf(RouteMatcherInterface::class, $factory($container));
    }
}
