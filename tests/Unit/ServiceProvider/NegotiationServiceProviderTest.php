<?php

declare(strict_types=1);

namespace App\Tests\Unit\ServiceProvider;

use App\ServiceProvider\NegotiationServiceProvider;
use Chubbyphp\Deserialization\DeserializerInterface;
use Chubbyphp\Mock\Call;
use Chubbyphp\Mock\MockByCallsTrait;
use Chubbyphp\Serialization\SerializerInterface;
use PHPUnit\Framework\TestCase;
use Pimple\Container;

/**
 * @covers \App\ServiceProvider\NegotiationServiceProvider
 *
 * @internal
 */
final class NegotiationServiceProviderTest extends TestCase
{
    use MockByCallsTrait;

    public function testRegister(): void
    {
        $container = new Container([
            'deserializer' => $this->getMockByCalls(DeserializerInterface::class, [
                Call::create('getContentTypes')->with()->willReturn(['application/json']),
            ]),
            'serializer' => $this->getMockByCalls(SerializerInterface::class, [
                Call::create('getContentTypes')->with()->willReturn(['application/json']),
            ]),
        ]);

        $serviceProvider = new NegotiationServiceProvider();
        $serviceProvider->register($container);

        self::assertArrayHasKey('negotiator.acceptNegotiator.values', $container);
        self::assertArrayHasKey('negotiator.contentTypeNegotiator.values', $container);

        self::assertSame(['application/json'], $container['negotiator.acceptNegotiator.values']);
        self::assertSame(['application/json'], $container['negotiator.contentTypeNegotiator.values']);
    }
}
