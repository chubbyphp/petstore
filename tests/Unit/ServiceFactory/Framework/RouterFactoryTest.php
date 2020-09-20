<?php

declare(strict_types=1);

namespace App\Tests\Unit\ServiceFactory\Framework;

use App\ServiceFactory\Framework\RouterFactory;
use Chubbyphp\Framework\Router\FastRoute\Router;
use Chubbyphp\Framework\Router\RouteInterface;
use Chubbyphp\Mock\Call;
use Chubbyphp\Mock\MockByCallsTrait;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;

/**
 * @covers \App\ServiceFactory\Framework\RouterFactory
 *
 * @internal
 */
final class RouterFactoryTest extends TestCase
{
    use MockByCallsTrait;

    public function testInvoke(): void
    {
        $config = [
            'fastroute' => [
                'cache' => sys_get_temp_dir().'/'.uniqid('fastroute-cache-').'.php',
            ],
        ];

        /** @var ContainerInterface $container */
        $container = $this->getMockByCalls(ContainerInterface::class, [
            Call::create('get')->with(RouteInterface::class.'[]')->willReturn([]),
            Call::create('get')->with('config')->willReturn($config),
        ]);

        $factory = new RouterFactory();

        self::assertInstanceOf(Router::class, $factory($container));
    }
}
