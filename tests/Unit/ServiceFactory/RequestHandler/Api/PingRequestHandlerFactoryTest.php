<?php

declare(strict_types=1);

namespace App\Tests\Unit\ServiceFactory\RequestHandler\Api;

use App\RequestHandler\Api\PingRequestHandler;
use App\ServiceFactory\RequestHandler\Api\PingRequestHandlerFactory;
use Chubbyphp\Mock\Call;
use Chubbyphp\Mock\MockByCallsTrait;
use Chubbyphp\Serialization\SerializerInterface;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseFactoryInterface;

/**
 * @covers \App\ServiceFactory\RequestHandler\Api\PingRequestHandlerFactory
 *
 * @internal
 */
final class PingRequestHandlerFactoryTest extends TestCase
{
    use MockByCallsTrait;

    public function testInvoke(): void
    {
        /** @var ResponseFactoryInterface $responseFactory */
        $responseFactory = $this->getMockByCalls(ResponseFactoryInterface::class);

        /** @var SerializerInterface $serializer */
        $serializer = $this->getMockByCalls(SerializerInterface::class);

        /** @var ContainerInterface $container */
        $container = $this->getMockByCalls(ContainerInterface::class, [
            Call::create('get')->with(ResponseFactoryInterface::class)->willReturn($responseFactory),
            Call::create('get')->with(SerializerInterface::class)->willReturn($serializer),
        ]);

        $factory = new PingRequestHandlerFactory();

        self::assertInstanceOf(PingRequestHandler::class, $factory($container));
    }
}
