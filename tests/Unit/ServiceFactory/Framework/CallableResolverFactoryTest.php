<?php

declare(strict_types=1);

namespace App\Tests\Unit\ServiceFactory\Framework;

use App\ServiceFactory\Framework\CallableResolverFactory;
use Chubbyphp\Mock\MockByCallsTrait;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;
use Slim\CallableResolver;

/**
 * @covers \App\ServiceFactory\Framework\CallableResolverFactory
 *
 * @internal
 */
final class CallableResolverFactoryTest extends TestCase
{
    use MockByCallsTrait;

    public function testInvoke(): void
    {
        /** @var ContainerInterface $container */
        $container = $this->getMockByCalls(ContainerInterface::class);

        $factory = new CallableResolverFactory();

        self::assertInstanceOf(CallableResolver::class, $factory($container));
    }
}
