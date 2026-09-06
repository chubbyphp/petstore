<?php

declare(strict_types=1);

namespace App\Tests\Unit\Core\ServiceFactory\Framework;

use App\Core\Middleware\ConvertHttpExceptionMiddleware;
use App\Core\ServiceFactory\Framework\MiddlewaresFactory;
use Chubbyphp\Cors\CorsMiddleware;
use Chubbyphp\Mock\MockMethod\WithReturn;
use Chubbyphp\Mock\MockObjectBuilder;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;
use Slim\Middleware\ErrorMiddleware;

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

        /** @var ErrorMiddleware $errorMiddleware */
        $errorMiddleware = $builder->create(ErrorMiddleware::class, []);

        /** @var ConvertHttpExceptionMiddleware $convertHttpExceptionMiddleware */
        $convertHttpExceptionMiddleware = $builder->create(ConvertHttpExceptionMiddleware::class, []);

        /** @var CorsMiddleware $corsMiddleware */
        $corsMiddleware = $builder->create(CorsMiddleware::class, []);

        /** @var ContainerInterface $container */
        $container = $builder->create(ContainerInterface::class, [
            new WithReturn('get', [CorsMiddleware::class], $corsMiddleware),
            new WithReturn('get', [ConvertHttpExceptionMiddleware::class], $convertHttpExceptionMiddleware),
            new WithReturn('get', [ErrorMiddleware::class], $errorMiddleware),
        ]);

        $factory = new MiddlewaresFactory();

        self::assertSame(
            [$corsMiddleware, $convertHttpExceptionMiddleware, $errorMiddleware],
            $factory($container)
        );
    }
}
