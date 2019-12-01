<?php

declare(strict_types=1);

namespace App\Tests\Unit\ServiceFactory;

use App\ServiceFactory\NegotiationServiceFactory;
use Chubbyphp\Deserialization\DeserializerInterface;
use Chubbyphp\Mock\Call;
use Chubbyphp\Mock\MockByCallsTrait;
use Chubbyphp\Serialization\SerializerInterface;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;

/**
 * @covers \App\ServiceFactory\NegotiationServiceFactory
 *
 * @internal
 */
final class NegotiationServiceFactoryTest extends TestCase
{
    use MockByCallsTrait;

    public function testFactories(): void
    {
        $factories = (new NegotiationServiceFactory())();

        self::assertCount(2, $factories);
    }

    public function testAcceptNegotiatorValues(): void
    {
        /** @var SerializerInterface|MockObject $serializer */
        $serializer = $this->getMockByCalls(SerializerInterface::class, [
            Call::create('getContentTypes')->with()->willReturn(['application/json']),
        ]);

        /** @var ContainerInterface|MockObject $container */
        $container = $this->getMockByCalls(ContainerInterface::class, [
            Call::create('get')->with('serializer')->willReturn($serializer),
        ]);

        $factories = (new NegotiationServiceFactory())();

        self::assertArrayHasKey('negotiator.acceptNegotiator.values', $factories);

        self::assertSame(
            ['application/json'],
            $factories['negotiator.acceptNegotiator.values']($container)
        );
    }

    public function testContentTypeNegotiatorValues(): void
    {
        /** @var DeserializerInterface|MockObject $deserializer */
        $deserializer = $this->getMockByCalls(DeserializerInterface::class, [
            Call::create('getContentTypes')->with()->willReturn(['application/json']),
        ]);

        /** @var ContainerInterface|MockObject $container */
        $container = $this->getMockByCalls(ContainerInterface::class, [
            Call::create('get')->with('deserializer')->willReturn($deserializer),
        ]);

        $factories = (new NegotiationServiceFactory())();

        self::assertArrayHasKey('negotiator.contentTypeNegotiator.values', $factories);

        self::assertSame(
            ['application/json'],
            $factories['negotiator.contentTypeNegotiator.values']($container)
        );
    }
}
