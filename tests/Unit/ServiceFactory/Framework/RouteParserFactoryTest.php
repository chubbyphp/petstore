<?php

declare(strict_types=1);

namespace App\Tests\Unit\ServiceFactory\Framework;

use App\ServiceFactory\Framework\RouteParserFactory;
use Chubbyphp\Mock\Call;
use Chubbyphp\Mock\MockByCallsTrait;
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
    use MockByCallsTrait;

    public function testInvoke(): void
    {
        /** @var RouteParserInterface $routeParser */
        $routeParser = $this->getMockByCalls(RouteParserInterface::class);

        /** @var RouteCollectorInterface $routeCollector */
        $routeCollector = $this->getMockByCalls(RouteCollectorInterface::class, [
            Call::create('getRouteParser')->with()->willReturn($routeParser),
        ]);

        /** @var ContainerInterface $container */
        $container = $this->getMockByCalls(ContainerInterface::class, [
            Call::create('get')->with(RouteCollectorInterface::class)->willReturn($routeCollector),
        ]);

        $factory = new RouteParserFactory();

        self::assertInstanceOf(RouteParserInterface::class, $factory($container));
    }
}
