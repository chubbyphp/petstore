<?php

declare(strict_types=1);

namespace App\Tests\Unit\Core\ServiceFactory\Framework;

use App\Core\RequestHandler\OpenapiRequestHandler;
use App\Core\RequestHandler\PingRequestHandler;
use App\Core\ServiceFactory\Framework\CallableResolverFactory;
use App\Core\ServiceFactory\Framework\RoutesDelegator;
use Chubbyphp\Laminas\Config\Config;
use Chubbyphp\Laminas\Config\ContainerFactory;
use Chubbyphp\Mock\MockMethod\WithCallback;
use Chubbyphp\Mock\MockObjectBuilder;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Slim\Handlers\Strategies\RequestHandler;
use Slim\Interfaces\CallableResolverInterface;
use Slim\Interfaces\RouteInterface;
use Slim\Psr7\Factory\ResponseFactory;
use Slim\Psr7\Factory\ServerRequestFactory;
use Slim\Routing\RouteCollector;

/**
 * @covers \App\Core\ServiceFactory\Framework\RoutesDelegator
 *
 * @internal
 */
final class RoutesDelegatorTest extends TestCase
{
    public function testInvoke(): void
    {
        $builder = new MockObjectBuilder();

        $log = [];

        $responseFactory = new ResponseFactory();

        $response = $responseFactory->createResponse();

        $handler = static function (string $name) use (&$log, $response): \Closure {
            return static function () use (&$log, $name, $response): ResponseInterface {
                $log[] = $name;

                return $response;
            };
        };

        /** @var RequestHandlerInterface $ping */
        $ping = $builder->create(RequestHandlerInterface::class, [new WithCallback('handle', $handler('ping'))]);

        /** @var RequestHandlerInterface $openApi */
        $openApi = $builder->create(RequestHandlerInterface::class, [new WithCallback('handle', $handler('openapi'))]);

        $container = (new ContainerFactory())(new Config([
            'dependencies' => [
                'services' => [
                    ResponseFactoryInterface::class => $responseFactory,
                    PingRequestHandler::class => $ping,
                    OpenapiRequestHandler::class => $openApi,
                ],
                'factories' => [
                    CallableResolverInterface::class => CallableResolverFactory::class,
                ],
            ],
        ]));

        /** @var CallableResolverInterface $callableResolver */
        $callableResolver = $container->get(CallableResolverInterface::class);

        $routeCollector = new RouteCollector(
            $responseFactory,
            $callableResolver,
            $container,
            new RequestHandler(true)
        );

        $factory = new RoutesDelegator();

        self::assertSame($routeCollector, $factory($container, '', static fn () => $routeCollector));

        /** @var array<RouteInterface> $routes */
        $routes = array_values($routeCollector->getRoutes());

        self::assertCount(2, $routes);

        $this->assertRoute($routes[0], ['GET'], '/ping', 'ping', PingRequestHandler::class);
        $this->assertRoute($routes[1], ['GET'], '/openapi', 'openapi', OpenapiRequestHandler::class);

        $serverRequestFactory = new ServerRequestFactory();

        self::assertSame($response, $routes[0]->run($serverRequestFactory->createServerRequest('GET', 'http://localhost/ping')));
        self::assertSame($response, $routes[1]->run($serverRequestFactory->createServerRequest('GET', 'http://localhost/openapi')));

        self::assertSame(['ping', 'openapi'], $log);
    }

    /**
     * @param array<string> $methods
     */
    private function assertRoute(RouteInterface $route, array $methods, string $pattern, string $name, string $callable): void
    {
        self::assertSame($methods, $route->getMethods());
        self::assertSame($pattern, $route->getPattern());
        self::assertSame($name, $route->getName());
        self::assertSame($callable, $route->getCallable());
    }
}
