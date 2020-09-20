<?php

declare(strict_types=1);

namespace App\Tests\Unit\ServiceFactory\Framework;

use App\ServiceFactory\Framework\MiddlewaresFactory;
use Chubbyphp\Cors\CorsMiddleware;
use Chubbyphp\Framework\Middleware\ExceptionMiddleware;
use Chubbyphp\Framework\Middleware\LazyMiddleware;
use Chubbyphp\Framework\Middleware\RouterMiddleware;
use Chubbyphp\Mock\MockByCallsTrait;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;

/**
 * @covers \App\ServiceFactory\Framework\MiddlewaresFactory
 *
 * @internal
 */
final class MiddlewaresFactoryTest extends TestCase
{
    use MockByCallsTrait;

    public function testInvoke(): void
    {
        /** @var ContainerInterface $container */
        $container = $this->getMockByCalls(ContainerInterface::class);

        $factory = new MiddlewaresFactory();

        self::assertEquals([
            new LazyMiddleware($container, ExceptionMiddleware::class),
            new LazyMiddleware($container, CorsMiddleware::class),
            new LazyMiddleware($container, RouterMiddleware::class),
        ], $factory($container));
    }
}
