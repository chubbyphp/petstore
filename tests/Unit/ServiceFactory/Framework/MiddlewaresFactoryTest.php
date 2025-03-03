<?php

declare(strict_types=1);

namespace App\Tests\Unit\ServiceFactory\Framework;

use App\ServiceFactory\Framework\MiddlewaresFactory;
use Chubbyphp\Cors\CorsMiddleware;
use Chubbyphp\Framework\Middleware\ExceptionMiddleware;
use Chubbyphp\Framework\Middleware\LazyMiddleware;
use Chubbyphp\Framework\Middleware\RouteMatcherMiddleware;
use Chubbyphp\Mock\MockObjectBuilder;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;

/**
 * @covers \App\ServiceFactory\Framework\MiddlewaresFactory
 *
 * @internal
 */
final class MiddlewaresFactoryTest extends TestCase
{
    public function testInvoke(): void
    {
        $builder = new MockObjectBuilder();

        /** @var ContainerInterface $container */
        $container = $builder->create(ContainerInterface::class, []);

        $factory = new MiddlewaresFactory();

        self::assertEquals(
            [
                new LazyMiddleware($container, ExceptionMiddleware::class),
                new LazyMiddleware($container, CorsMiddleware::class),
                new LazyMiddleware($container, RouteMatcherMiddleware::class),
            ],
            $factory($container)
        );
    }
}
