<?php

declare(strict_types=1);

namespace App\Tests\Unit\ServiceFactory\Middleware;

use App\Middleware\ApiExceptionMiddleware;
use App\ServiceFactory\Middleware\ApiExceptionMiddlewareFactory;
use Chubbyphp\DecodeEncode\Encoder\EncoderInterface;
use Chubbyphp\Mock\MockMethod\WithReturn;
use Chubbyphp\Mock\MockObjectBuilder;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Log\LoggerInterface;

/**
 * @covers \App\ServiceFactory\Middleware\ApiExceptionMiddlewareFactory
 *
 * @internal
 */
final class ApiExceptionMiddlewareFactoryTest extends TestCase
{
    public function testInvoke(): void
    {
        $builder = new MockObjectBuilder();

        /** @var EncoderInterface $encoder */
        $encoder = $builder->create(EncoderInterface::class, []);

        /** @var ResponseFactoryInterface $responseFactory */
        $responseFactory = $builder->create(ResponseFactoryInterface::class, []);

        /** @var LoggerInterface $logger */
        $logger = $builder->create(LoggerInterface::class, []);

        /** @var ContainerInterface $container */
        $container = $builder->create(ContainerInterface::class, [
            new WithReturn('get', [EncoderInterface::class], $encoder),
            new WithReturn('get', [ResponseFactoryInterface::class], $responseFactory),
            new WithReturn('get', ['config'], ['debug' => true]),
            new WithReturn('get', [LoggerInterface::class], $logger),
        ]);

        $factory = new ApiExceptionMiddlewareFactory();

        self::assertInstanceOf(ApiExceptionMiddleware::class, $factory($container));
    }
}
