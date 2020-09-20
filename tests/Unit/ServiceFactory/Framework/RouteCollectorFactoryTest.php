<?php

declare(strict_types=1);

namespace App\Tests\Unit\ServiceFactory\Framework;

use App\ServiceFactory\Framework\RouteCollectorFactory;
use Chubbyphp\Mock\Call;
use Chubbyphp\Mock\MockByCallsTrait;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseFactoryInterface;
use Slim\Interfaces\CallableResolverInterface;
use Slim\Routing\RouteCollector;

/**
 * @covers \App\ServiceFactory\Framework\RouteCollectorFactory
 *
 * @internal
 */
final class RouteCollectorFactoryTest extends TestCase
{
    use MockByCallsTrait;

    public function testInvoke(): void
    {
        /** @var ResponseFactoryInterface $responseFactory */
        $responseFactory = $this->getMockByCalls(ResponseFactoryInterface::class);

        /** @var CallableResolverInterface $callableResolver */
        $callableResolver = $this->getMockByCalls(CallableResolverInterface::class);

        /** @var ContainerInterface $container */
        $container = $this->getMockByCalls(ContainerInterface::class, [
            Call::create('get')->with(ResponseFactoryInterface::class)->willReturn($responseFactory),
            Call::create('get')->with(CallableResolverInterface::class)->willReturn($callableResolver),
            Call::create('get')->with('config')->willReturn([
                'fastroute' => ['cache' => sys_get_temp_dir().'/'.uniqid('fastroute-')],
            ]),
        ]);

        $factory = new RouteCollectorFactory();

        self::assertInstanceOf(RouteCollector::class, $factory($container));
    }
}
