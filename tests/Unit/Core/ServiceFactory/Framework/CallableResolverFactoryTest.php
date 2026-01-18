<?php

declare(strict_types=1);

namespace App\Tests\Unit\Core\ServiceFactory\Framework;

use App\Core\ServiceFactory\Framework\CallableResolverFactory;
use Chubbyphp\Mock\MockObjectBuilder;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;
use Slim\CallableResolver;

/**
 * @covers \App\Core\ServiceFactory\Framework\CallableResolverFactory
 *
 * @internal
 */
final class CallableResolverFactoryTest extends TestCase
{
    public function testInvoke(): void
    {
        $builder = new MockObjectBuilder();

        /** @var ContainerInterface $container */
        $container = $builder->create(ContainerInterface::class, []);

        $factory = new CallableResolverFactory();

        self::assertInstanceOf(CallableResolver::class, $factory($container));
    }
}
