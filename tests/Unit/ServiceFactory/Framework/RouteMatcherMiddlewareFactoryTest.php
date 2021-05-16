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
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Log\LoggerInterface;

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
        /** @var RouteMatcherInterface|MockObject $routeMatcher */
        $routeMatcher = $this->getMockByCalls(RouteMatcherInterface::class);

        /** @var ResponseFactoryInterface $responseFactory */
        $responseFactory = $this->getMockByCalls(ResponseFactoryInterface::class);

        /** @var LoggerInterface $logger */
        $logger = $this->getMockByCalls(LoggerInterface::class);

        /** @var ContainerInterface $container */
        $container = $this->getMockByCalls(ContainerInterface::class, [
            Call::create('get')->with(RouteMatcherInterface::class)->willReturn($routeMatcher),
            Call::create('get')->with(ResponseFactoryInterface::class)->willReturn($responseFactory),
            Call::create('get')->with(LoggerInterface::class)->willReturn($logger),
        ]);

        $factory = new RouteMatcherMiddlewareFactory();

        self::assertInstanceOf(RouteMatcherMiddleware::class, $factory($container));
    }
}
