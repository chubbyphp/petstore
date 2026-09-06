<?php

declare(strict_types=1);

namespace App\Tests\Unit\Core\ServiceFactory\Framework;

use App\Core\ServiceFactory\Framework\ErrorMiddlewareFactory;
use Chubbyphp\Mock\MockMethod\WithReturn;
use Chubbyphp\Mock\MockObjectBuilder;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Log\LoggerInterface;
use Slim\Interfaces\CallableResolverInterface;
use Slim\Middleware\ErrorMiddleware;

/**
 * @covers \App\Core\ServiceFactory\Framework\ErrorMiddlewareFactory
 *
 * @internal
 */
final class ErrorMiddlewareFactoryTest extends TestCase
{
    public function testInvoke(): void
    {
        $builder = new MockObjectBuilder();

        /** @var CallableResolverInterface $callableResolver */
        $callableResolver = $builder->create(CallableResolverInterface::class, []);

        /** @var ResponseFactoryInterface $responseFactory */
        $responseFactory = $builder->create(ResponseFactoryInterface::class, []);

        /** @var LoggerInterface $logger */
        $logger = $builder->create(LoggerInterface::class, []);

        /** @var ContainerInterface $container */
        $container = $builder->create(ContainerInterface::class, [
            new WithReturn('get', [CallableResolverInterface::class], $callableResolver),
            new WithReturn('get', [ResponseFactoryInterface::class], $responseFactory),
            new WithReturn('get', ['config'], ['debug' => true]),
            new WithReturn('get', [LoggerInterface::class], $logger),
        ]);

        $factory = new ErrorMiddlewareFactory();

        $errorMiddleware = $factory($container);

        $getter = \Closure::bind(
            fn ($property) => $this->{$property},
            $errorMiddleware,
            ErrorMiddleware::class
        );

        self::assertSame($callableResolver, $getter('callableResolver'));
        self::assertSame($responseFactory, $getter('responseFactory'));
        self::assertTrue($getter('displayErrorDetails'));
        self::assertTrue($getter('logErrors'));
        self::assertTrue($getter('logErrorDetails'));
        self::assertSame($logger, $getter('logger'));
    }
}
