<?php

declare(strict_types=1);

namespace App\Tests\Unit\ServiceFactory\Http;

use App\ServiceFactory\Http\StreamFactoryFactory;
use Chubbyphp\Mock\MockObjectBuilder;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;
use Slim\Psr7\Factory\StreamFactory;

/**
 * @covers \App\ServiceFactory\Http\StreamFactoryFactory
 *
 * @internal
 */
final class StreamFactoryFactoryTest extends TestCase
{
    public function testInvoke(): void
    {
        $builder = new MockObjectBuilder();

        /** @var ContainerInterface $container */
        $container = $builder->create(ContainerInterface::class, []);

        $factory = new StreamFactoryFactory();

        self::assertInstanceOf(StreamFactory::class, $factory());
    }
}
