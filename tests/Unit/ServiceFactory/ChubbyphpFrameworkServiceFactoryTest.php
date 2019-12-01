<?php

declare(strict_types=1);

namespace App\Tests\Unit\ServiceFactory;

use App\ServiceFactory\ChubbyphpFrameworkServiceFactory;
use Chubbyphp\Framework\Middleware\ExceptionMiddleware;
use Chubbyphp\Framework\Middleware\RouterMiddleware;
use Chubbyphp\Framework\Router\RouterInterface;
use Chubbyphp\Mock\Call;
use Chubbyphp\Mock\MockByCallsTrait;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Log\LoggerInterface;

/**
 * @covers \App\ServiceFactory\ChubbyphpFrameworkServiceFactory
 *
 * @internal
 */
final class ChubbyphpFrameworkServiceFactoryTest extends TestCase
{
    use MockByCallsTrait;

    public function testFactories(): void
    {
        $factories = (new ChubbyphpFrameworkServiceFactory())();

        self::assertCount(4, $factories);
    }

    public function testExceptionMiddleware(): void
    {
        /** @var ResponseFactoryInterface|MockObject $responseFactory */
        $responseFactory = $this->getMockByCalls(ResponseFactoryInterface::class);

        /** @var LoggerInterface|MockObject $logger */
        $logger = $this->getMockByCalls(LoggerInterface::class);

        /** @var ContainerInterface|MockObject $container */
        $container = $this->getMockByCalls(ContainerInterface::class, [
            Call::create('get')->with('api-http.response.factory')->willReturn($responseFactory),
            Call::create('get')->with('debug')->willReturn(true),
            Call::create('get')->with('logger')->willReturn($logger),
        ]);

        $factories = (new ChubbyphpFrameworkServiceFactory())();

        self::assertArrayHasKey(ExceptionMiddleware::class, $factories);

        self::assertInstanceOf(
            ExceptionMiddleware::class,
            $factories[ExceptionMiddleware::class]($container)
        );
    }

    public function testRouterMiddleware(): void
    {
        /** @var RouterInterface|MockObject $router */
        $router = $this->getMockByCalls(RouterInterface::class);

        /** @var ResponseFactoryInterface|MockObject $responseFactory */
        $responseFactory = $this->getMockByCalls(ResponseFactoryInterface::class);

        /** @var ContainerInterface|MockObject $container */
        $container = $this->getMockByCalls(ContainerInterface::class, [
            Call::create('get')->with(RouterInterface::class)->willReturn($router),
            Call::create('get')->with('api-http.response.factory')->willReturn($responseFactory),
        ]);

        $factories = (new ChubbyphpFrameworkServiceFactory())();

        self::assertArrayHasKey(RouterMiddleware::class, $factories);

        self::assertInstanceOf(
            RouterMiddleware::class,
            $factories[RouterMiddleware::class]($container)
        );
    }

    public function testRouter(): void
    {
        /** @var ContainerInterface|MockObject $container */
        $container = $this->getMockByCalls(ContainerInterface::class, [
            Call::create('get')->with('routes')->willReturn([]),
            Call::create('get')->with('routerCacheFile')->willReturn(sys_get_temp_dir().'/router-'.uniqid().uniqid()),
        ]);

        $factories = (new ChubbyphpFrameworkServiceFactory())();

        self::assertArrayHasKey(RouterInterface::class, $factories);

        self::assertInstanceOf(
            RouterInterface::class,
            $factories[RouterInterface::class]($container)
        );
    }

    public function testRoutes(): void
    {
        /** @var ContainerInterface|MockObject $container */
        $container = $this->getMockByCalls(ContainerInterface::class);

        $factories = (new ChubbyphpFrameworkServiceFactory())();

        self::assertArrayHasKey('routes', $factories);

        $routes = $factories['routes']($container);

        self::assertIsArray($routes);

        self::assertCount(9, $routes);
    }
}
