<?php

declare(strict_types=1);

namespace App\Tests\Unit\Core\ServiceFactory\Framework;

use App\Core\ServiceFactory\Framework\MiddlewaresFactory;
use Chubbyphp\Cors\CorsMiddleware;
use Chubbyphp\Mock\MockMethod\WithReturn;
use Chubbyphp\Mock\MockObjectBuilder;
use Laminas\Stratigility\Middleware\ErrorHandler;
use Mezzio\Handler\NotFoundHandler;
use Mezzio\MiddlewareContainer;
use Mezzio\MiddlewareFactory;
use Mezzio\MiddlewareFactoryInterface;
use Mezzio\Router\Middleware\DispatchMiddleware;
use Mezzio\Router\Middleware\MethodNotAllowedMiddleware;
use Mezzio\Router\Middleware\RouteMiddleware;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;

/**
 * @covers \App\Core\ServiceFactory\Framework\MiddlewaresFactory
 *
 * @internal
 */
final class MiddlewaresFactoryTest extends TestCase
{
    public function testInvoke(): void
    {
        $builder = new MockObjectBuilder();

        /** @var ContainerInterface $middlewareContainerContainer */
        $middlewareContainerContainer = $builder->create(ContainerInterface::class, []);

        $middlewareFactory = new MiddlewareFactory(new MiddlewareContainer($middlewareContainerContainer));

        /** @var ContainerInterface $container */
        $container = $builder->create(ContainerInterface::class, [
            new WithReturn('get', [MiddlewareFactoryInterface::class], $middlewareFactory),
        ]);

        $factory = new MiddlewaresFactory();

        self::assertEquals(
            [
                $middlewareFactory->lazy(ErrorHandler::class),
                $middlewareFactory->lazy(CorsMiddleware::class),
                $middlewareFactory->lazy(RouteMiddleware::class),
                $middlewareFactory->lazy(MethodNotAllowedMiddleware::class),
                $middlewareFactory->lazy(DispatchMiddleware::class),
                $middlewareFactory->lazy(NotFoundHandler::class),
            ],
            $factory($container)
        );
    }
}
