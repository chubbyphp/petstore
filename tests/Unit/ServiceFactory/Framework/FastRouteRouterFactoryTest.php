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

    public function testInvoke(): void
    {
        /** @var ContainerInterface $container */
        $container = $this->getMockByCalls(ContainerInterface::class, [
            Call::create('get')->with('config')->willReturn([
                'fastroute' => ['cache' => sys_get_temp_dir().'/'.uniqid('fastroute-')],
            ]),
        ]);

        $factory = new FastRouteRouterFactory();

        self::assertInstanceOf(FastRouteRouter::class, $factory($container));
    }
}
