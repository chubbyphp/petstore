<?php

declare(strict_types=1);

namespace App\Tests\Unit\ServiceFactory\Framework;

use App\ServiceFactory\Framework\FastRouteRouterFactory;
use Chubbyphp\Mock\Call;
use Chubbyphp\Mock\MockByCallsTrait;
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
    use MockByCallsTrait;

    public function testInvokeWithoutCache(): void
    {
        /** @var ContainerInterface $container */
        $container = $this->getMockByCalls(ContainerInterface::class, [
            Call::create('get')->with('config')->willReturn([
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

        /** @var ContainerInterface $container */
        $container = $this->getMockByCalls(ContainerInterface::class, [
            Call::create('get')->with('config')->willReturn([
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
