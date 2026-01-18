<?php

declare(strict_types=1);

namespace App\Tests\Unit\Core\ServiceFactory\Framework;

use App\Core\ServiceFactory\Framework\RoutesByNameFactory;
use Chubbyphp\Framework\Router\RouteInterface;
use Chubbyphp\Mock\MockMethod\WithReturn;
use Chubbyphp\Mock\MockObjectBuilder;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;

/**
 * @covers \App\Core\ServiceFactory\Framework\RoutesByNameFactory
 *
 * @internal
 */
final class RoutesByNameFactoryTest extends TestCase
{
    public function testInvoke(): void
    {
        $builder = new MockObjectBuilder();

        /** @var ContainerInterface $container */
        $container = $builder->create(ContainerInterface::class, [
            new WithReturn('get', [RouteInterface::class.'[]'], []),
        ]);

        $factory = new RoutesByNameFactory();

        self::assertEquals([], $factory($container)->getRoutesByName());
    }
}
