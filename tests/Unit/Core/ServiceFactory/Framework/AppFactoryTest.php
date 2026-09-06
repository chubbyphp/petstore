<?php

declare(strict_types=1);

namespace App\Tests\Unit\Core\ServiceFactory\Framework;

use App\Core\ServiceFactory\Framework\AppFactory;
use Chubbyphp\Mock\MockMethod\WithCallback;
use Chubbyphp\Mock\MockMethod\WithReturn;
use Chubbyphp\Mock\MockObjectBuilder;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Slim\App;
use Slim\CallableResolver;
use Slim\Interfaces\CallableResolverInterface;
use Slim\Interfaces\RouteCollectorInterface;
use Slim\Psr7\Factory\ResponseFactory;
use Slim\Psr7\Factory\ServerRequestFactory;
use Slim\Routing\RouteCollector;

/**
 * @covers \App\Core\ServiceFactory\Framework\AppFactory
 *
 * @internal
 */
final class AppFactoryTest extends TestCase
{
    public function testInvoke(): void
    {
        $builder = new MockObjectBuilder();

        $log = [];

        $record = static function (string $name) use (&$log): \Closure {
            return static function (ServerRequestInterface $request, RequestHandlerInterface $handler) use (&$log, $name): ResponseInterface {
                $log[] = $name;

                return $handler->handle($request);
            };
        };

        /** @var MiddlewareInterface $outerMiddleware */
        $outerMiddleware = $builder->create(MiddlewareInterface::class, [new WithCallback('process', $record('outer'))]);

        /** @var MiddlewareInterface $middleMiddleware */
        $middleMiddleware = $builder->create(MiddlewareInterface::class, [new WithCallback('process', $record('middle'))]);

        /** @var MiddlewareInterface $innerMiddleware */
        $innerMiddleware = $builder->create(MiddlewareInterface::class, [new WithCallback('process', $record('inner'))]);

        $responseFactory = new ResponseFactory();
        $callableResolver = new CallableResolver();

        $response = $responseFactory->createResponse();

        /** @var RequestHandlerInterface $handler */
        $handler = $builder->create(RequestHandlerInterface::class, [
            new WithCallback('handle', static function () use (&$log, $response): ResponseInterface {
                $log[] = 'handler';

                return $response;
            }),
        ]);

        $routeCollector = new RouteCollector($responseFactory, $callableResolver);
        $routeCollector->map(['GET'], '/', $handler);

        /** @var ContainerInterface $container */
        $container = $builder->create(ContainerInterface::class, [
            new WithReturn('get', [ResponseFactoryInterface::class], $responseFactory),
            new WithReturn('get', [CallableResolverInterface::class], $callableResolver),
            new WithReturn('get', [RouteCollectorInterface::class], $routeCollector),
            new WithReturn('get', [MiddlewareInterface::class.'[]'], [$innerMiddleware, $middleMiddleware, $outerMiddleware]),
        ]);

        $factory = new AppFactory();

        $app = $factory($container);

        self::assertInstanceOf(App::class, $app);
        self::assertSame($responseFactory, $app->getResponseFactory());
        self::assertSame($callableResolver, $app->getCallableResolver());
        self::assertSame($container, $app->getContainer());
        self::assertSame($routeCollector, $app->getRouteCollector());

        $request = (new ServerRequestFactory())->createServerRequest('GET', 'http://localhost/');

        self::assertSame($response, $app->handle($request));
        self::assertSame(['outer', 'middle', 'inner', 'handler'], $log);
    }
}
