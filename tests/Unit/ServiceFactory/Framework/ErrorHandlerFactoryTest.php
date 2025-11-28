<?php

declare(strict_types=1);

namespace App\Tests\Unit\ServiceFactory\Framework;

use App\ServiceFactory\Framework\ErrorHandlerFactory;
use Chubbyphp\Mock\MockMethod\WithReturn;
use Chubbyphp\Mock\MockObjectBuilder;
use Laminas\Stratigility\Middleware\ErrorHandler;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseFactoryInterface;

/**
 * @covers \App\ServiceFactory\Framework\ErrorHandlerFactory
 *
 * @internal
 */
final class ErrorHandlerFactoryTest extends TestCase
{
    public function testInvoke(): void
    {
        $builder = new MockObjectBuilder();

        /** @var ResponseFactoryInterface $response */
        $responseFactory = $builder->create(ResponseFactoryInterface::class, []);

        /** @var ContainerInterface $container */
        $container = $builder->create(ContainerInterface::class, [
            new WithReturn(
                'get',
                [ResponseFactoryInterface::class],
                $responseFactory
            ),
            new WithReturn('get', ['config'], ['debug' => true]),
        ]);

        $factory = new ErrorHandlerFactory();

        self::assertInstanceOf(ErrorHandler::class, $factory($container));
    }
}
