<?php

declare(strict_types=1);

namespace App\Tests\Unit\Core\ServiceFactory\Framework;

use App\Core\ServiceFactory\Framework\ApplicationPipelineFactory;
use Chubbyphp\Mock\MockMethod\WithReturn;
use Chubbyphp\Mock\MockObjectBuilder;
use Laminas\Stratigility\MiddlewarePipe;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;
use Psr\Http\Server\MiddlewareInterface;

/**
 * @covers \App\Core\ServiceFactory\Framework\ApplicationPipelineFactory
 *
 * @internal
 */
final class ApplicationPipelineFactoryTest extends TestCase
{
    public function testInvoke(): void
    {
        $builder = new MockObjectBuilder();

        /** @var MiddlewareInterface $middleware1 */
        $middleware1 = $builder->create(MiddlewareInterface::class, []);

        /** @var MiddlewareInterface $middleware2 */
        $middleware2 = $builder->create(MiddlewareInterface::class, []);

        /** @var ContainerInterface $container */
        $container = $builder->create(ContainerInterface::class, [
            new WithReturn('get', [MiddlewareInterface::class.'[]'], [$middleware1, $middleware2]),
        ]);

        $factory = new ApplicationPipelineFactory();

        $pipeline = $factory($container);

        self::assertInstanceOf(MiddlewarePipe::class, $pipeline);
        self::assertSame([$middleware1, $middleware2], iterator_to_array($pipeline, false));
    }
}
