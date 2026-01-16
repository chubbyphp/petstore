<?php

declare(strict_types=1);

namespace App\Tests\Unit\ServiceFactory\Framework;

use App\ServiceFactory\Framework\NotFoundHandlerFactory;
use Chubbyphp\Mock\MockMethod\WithReturn;
use Chubbyphp\Mock\MockObjectBuilder;
use Mezzio\Handler\NotFoundHandler;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseFactoryInterface;

/**
 * @covers \App\ServiceFactory\Framework\NotFoundHandlerFactory
 *
 * @internal
 */
final class NotFoundHandlerFactoryTest extends TestCase
{
    public function testInvoke(): void
    {
        $builder = new MockObjectBuilder();

        /** @var ResponseFactoryInterface $responseFactory */
        $responseFactory = $builder->create(ResponseFactoryInterface::class, []);

        /** @var ContainerInterface $container */
        $container = $builder->create(ContainerInterface::class, [
            new WithReturn(
                'get',
                [ResponseFactoryInterface::class],
                static fn () => $responseFactory
            ),
        ]);

        $factory = new NotFoundHandlerFactory();

        self::assertInstanceOf(NotFoundHandler::class, $factory($container));
    }
}
