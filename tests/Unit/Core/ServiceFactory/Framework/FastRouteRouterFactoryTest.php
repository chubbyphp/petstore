<?php

declare(strict_types=1);

namespace App\Tests\Unit\Core\ServiceFactory\Framework;

use App\Core\ServiceFactory\Framework\FastRouteRouterFactory;
use Chubbyphp\Mock\MockMethod\WithReturn;
use Chubbyphp\Mock\MockObjectBuilder;
use Mezzio\Router\FastRouteRouter;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;

/**
 * @covers \App\ServiceFactory\Framework\FastRouteRouterFactory
 *
 * @internal
 */
final class FastRouteRouterFactoryTest extends TestCase
{
    public function testInvokeWithoutCache(): void
    {
        $builder = new MockObjectBuilder();

        /** @var ContainerInterface $container */
        $container = $builder->create(ContainerInterface::class, [
            new WithReturn('get', ['config'], [
                'fastroute' => ['cache' => null],
            ]),
        ]);

        $factory = new FastRouteRouterFactory();

        /** @var FastRouteRouter $fastRouteRouter */
        $fastRouteRouter = $factory($container);

        self::assertInstanceOf(FastRouteRouter::class, $fastRouteRouter);

        $getter = \Closure::bind(
            fn ($property) => $this->{$property},
            $fastRouteRouter,
            $fastRouteRouter::class
        );

        self::assertFalse($getter('cacheEnabled'));
        self::assertSame('data/cache/fastroute.php.cache', $getter('cacheFile'));
    }

    public function testInvokeWithCache(): void
    {
        $cachePath = sys_get_temp_dir().'/'.uniqid('fastroute-');
        $builder = new MockObjectBuilder();

        /** @var ContainerInterface $container */
        $container = $builder->create(ContainerInterface::class, [
            new WithReturn('get', ['config'], [
                'fastroute' => ['cache' => $cachePath],
            ]),
        ]);

        $factory = new FastRouteRouterFactory();

        /** @var FastRouteRouter $fastRouteRouter */
        $fastRouteRouter = $factory($container);

        self::assertInstanceOf(FastRouteRouter::class, $fastRouteRouter);

        $getter = \Closure::bind(
            fn ($property) => $this->{$property},
            $fastRouteRouter,
            $fastRouteRouter::class
        );

        self::assertTrue($getter('cacheEnabled'));
        self::assertSame($cachePath, $getter('cacheFile'));
    }
}
