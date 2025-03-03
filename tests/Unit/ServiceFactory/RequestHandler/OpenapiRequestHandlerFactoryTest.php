<?php

declare(strict_types=1);

namespace App\Tests\Unit\ServiceFactory\RequestHandler;

use App\RequestHandler\OpenapiRequestHandler;
use App\ServiceFactory\RequestHandler\OpenapiRequestHandlerFactory;
use Chubbyphp\Mock\MockMethod\WithReturn;
use Chubbyphp\Mock\MockObjectBuilder;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\StreamFactoryInterface;

/**
 * @covers \App\ServiceFactory\RequestHandler\OpenapiRequestHandlerFactory
 *
 * @internal
 */
final class OpenapiRequestHandlerFactoryTest extends TestCase
{
    public function testInvoke(): void
    {
        $builder = new MockObjectBuilder();

        /** @var ResponseFactoryInterface $responseFactory */
        $responseFactory = $builder->create(ResponseFactoryInterface::class, []);

        /** @var StreamFactoryInterface $stream */
        $stream = $builder->create(StreamFactoryInterface::class, []);

        /** @var ContainerInterface $container */
        $container = $builder->create(ContainerInterface::class, [
            new WithReturn('get', [ResponseFactoryInterface::class], $responseFactory),
            new WithReturn('get', [StreamFactoryInterface::class], $stream),
        ]);

        $factory = new OpenapiRequestHandlerFactory();

        self::assertInstanceOf(OpenapiRequestHandler::class, $factory($container));
    }
}
