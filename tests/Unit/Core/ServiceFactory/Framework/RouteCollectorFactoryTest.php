<?php

declare(strict_types=1);

namespace App\Tests\Unit\Core\ServiceFactory\Framework;

use App\Core\ServiceFactory\Framework\RouteCollectorFactory;
use Chubbyphp\Mock\MockMethod\WithReturn;
use Chubbyphp\Mock\MockObjectBuilder;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseFactoryInterface;
use Slim\Interfaces\CallableResolverInterface;
use Slim\Interfaces\InvocationStrategyInterface;
use Slim\Routing\RouteCollector;

/**
 * @covers \App\Core\ServiceFactory\Framework\RouteCollectorFactory
 *
 * @internal
 */
final class RouteCollectorFactoryTest extends TestCase
{
    public function testInvoke(): void
    {
        $builder = new MockObjectBuilder();

        /** @var ResponseFactoryInterface $responseFactory */
        $responseFactory = $builder->create(ResponseFactoryInterface::class, []);

        /** @var CallableResolverInterface $callableResolver */
        $callableResolver = $builder->create(CallableResolverInterface::class, []);

        /** @var InvocationStrategyInterface $invocationStrategy */
        $invocationStrategy = $builder->create(InvocationStrategyInterface::class, []);

        /** @var ContainerInterface $container */
        $container = $builder->create(ContainerInterface::class, [
            new WithReturn('get', [ResponseFactoryInterface::class], $responseFactory),
            new WithReturn(
                'get',
                [CallableResolverInterface::class],
                $callableResolver
            ),
            new WithReturn(
                'get',
                [InvocationStrategyInterface::class],
                $invocationStrategy
            ),
            new WithReturn('get', ['config'], ['fastroute' => ['cache' => sys_get_temp_dir().'/'.uniqid('fastroute-')]]),
        ]);

        $factory = new RouteCollectorFactory();

        self::assertInstanceOf(RouteCollector::class, $factory($container));
    }
}
