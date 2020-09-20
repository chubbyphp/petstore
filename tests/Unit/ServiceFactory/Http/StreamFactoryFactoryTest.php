<?php

declare(strict_types=1);

namespace App\Tests\Unit\ServiceFactory\Http;

use App\ServiceFactory\Http\StreamFactoryFactory;
use Chubbyphp\Mock\MockByCallsTrait;
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
    use MockByCallsTrait;

    public function testInvoke(): void
    {
        /** @var ContainerInterface $container */
        $container = $this->getMockByCalls(ContainerInterface::class);

        $factory = new StreamFactoryFactory();

        self::assertInstanceOf(StreamFactory::class, $factory($container));
    }
}
