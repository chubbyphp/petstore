<?php

declare(strict_types=1);

namespace App\Tests\Unit\ServiceFactory\Http;

use App\ServiceFactory\Http\ResponseFactoryFactory;
use Chubbyphp\Mock\MockObjectBuilder;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;
use Slim\Psr7\Factory\ResponseFactory;

/**
 * @covers \App\ServiceFactory\Http\ResponseFactoryFactory
 *
 * @internal
 */
final class ResponseFactoryFactoryTest extends TestCase
{
    public function testInvoke(): void
    {
        $builder = new MockObjectBuilder();

        /** @var ContainerInterface $container */
        $container = $builder->create(ContainerInterface::class, []);

        $factory = new ResponseFactoryFactory();

        self::assertInstanceOf(ResponseFactory::class, $factory());
    }
}
