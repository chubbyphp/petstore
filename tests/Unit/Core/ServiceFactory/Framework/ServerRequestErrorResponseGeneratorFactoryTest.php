<?php

declare(strict_types=1);

namespace App\Tests\Unit\Core\ServiceFactory\Framework;

use App\Core\ServiceFactory\Framework\ServerRequestErrorResponseGeneratorFactory;
use Chubbyphp\Mock\MockMethod\WithReturn;
use Chubbyphp\Mock\MockObjectBuilder;
use Mezzio\Response\ServerRequestErrorResponseGenerator;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseFactoryInterface;

/**
 * @covers \App\ServiceFactory\Framework\ServerRequestErrorResponseGeneratorFactory
 *
 * @internal
 */
final class ServerRequestErrorResponseGeneratorFactoryTest extends TestCase
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
            new WithReturn('get', ['config'], ['debug' => true]),
        ]);

        $factory = new ServerRequestErrorResponseGeneratorFactory();

        self::assertInstanceOf(ServerRequestErrorResponseGenerator::class, $factory($container));
    }
}
