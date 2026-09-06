<?php

declare(strict_types=1);

namespace App\Tests\Unit\Core\ServiceFactory\Framework;

use App\Core\ServiceFactory\Framework\RoutesFactory;
use Chubbyphp\Mock\MockObjectBuilder;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;

/**
 * @covers \App\Core\ServiceFactory\Framework\RoutesFactory
 *
 * @internal
 */
final class RoutesFactoryTest extends TestCase
{
    public function testInvoke(): void
    {
        $builder = new MockObjectBuilder();

        /** @var ContainerInterface $container */
        $container = $builder->create(ContainerInterface::class, []);

        $factory = new RoutesFactory();

        self::assertEquals([], $factory($container));
    }
}
