<?php

declare(strict_types=1);

namespace App\Tests\Unit\ServiceFactory\RequestHandler;

use App\RequestHandler\PingRequestHandler;
use App\ServiceFactory\RequestHandler\PingRequestHandlerFactory;
use Chubbyphp\Mock\MockMethod\WithReturn;
use Chubbyphp\Mock\MockObjectBuilder;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseFactoryInterface;

/**
 * @covers \App\ServiceFactory\RequestHandler\PingRequestHandlerFactory
 *
 * @internal
 */
final class PingRequestHandlerFactoryTest extends TestCase
{
    public function testInvoke(): void
    {
        $builder = new MockObjectBuilder();

        /** @var ResponseFactoryInterface $responseFactory */
        $responseFactory = $builder->create(ResponseFactoryInterface::class, []);

        /** @var ContainerInterface $container */
        $container = $builder->create(ContainerInterface::class, [
            new WithReturn('get', [ResponseFactoryInterface::class], $responseFactory),
        ]);

        $factory = new PingRequestHandlerFactory();

        self::assertInstanceOf(PingRequestHandler::class, $factory($container));
    }
}
