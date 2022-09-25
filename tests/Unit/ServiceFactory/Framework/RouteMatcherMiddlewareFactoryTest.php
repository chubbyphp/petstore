<?php

declare(strict_types=1);

namespace App\Tests\Unit\ServiceFactory\Framework;

use App\ServiceFactory\Framework\RouteMatcherMiddlewareFactory;
use Chubbyphp\Framework\Middleware\RouteMatcherMiddleware;
use Chubbyphp\Framework\Router\RouteMatcherInterface;
use Chubbyphp\Mock\Call;
use Chubbyphp\Mock\MockByCallsTrait;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;

/**
 * @covers \App\ServiceFactory\Framework\RouteMatcherMiddlewareFactory
 *
 * @internal
 */
final class RouteMatcherMiddlewareFactoryTest extends TestCase
{
    use MockByCallsTrait;

    public function testInvoke(): void
    {
        /** @var MockObject|RouteMatcherInterface $routeMatcher */
        $routeMatcher = $this->getMockByCalls(RouteMatcherInterface::class);

        /** @var ContainerInterface $container */
        $container = $this->getMockByCalls(ContainerInterface::class, [
            Call::create('get')->with(RouteMatcherInterface::class)->willReturn($routeMatcher),
        ]);

        $factory = new RouteMatcherMiddlewareFactory();

        self::assertInstanceOf(RouteMatcherMiddleware::class, $factory($container));
    }
}
