<?php

declare(strict_types=1);

namespace App\Tests\Unit\ServiceFactory\Negotiation;

use App\ServiceFactory\Negotiation\AcceptNegotiatorSupportedMediaTypesFactory;
use Chubbyphp\Mock\Call;
use Chubbyphp\Mock\MockByCallsTrait;
use Chubbyphp\Serialization\Encoder\EncoderInterface;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;

/**
 * @covers \App\ServiceFactory\Negotiation\AcceptNegotiatorSupportedMediaTypesFactory
 *
 * @internal
 */
final class AcceptNegotiatorSupportedMediaTypesFactoryTest extends TestCase
{
    use MockByCallsTrait;

    public function testInvoke(): void
    {
        /** @var EncoderInterface $encoder */
        $encoder = $this->getMockByCalls(EncoderInterface::class, [
            Call::create('getContentTypes')->with()->willReturn(['application/json']),
        ]);

        /** @var ContainerInterface $container */
        $container = $this->getMockByCalls(ContainerInterface::class, [
            Call::create('has')->with(EncoderInterface::class)->willReturn(true),
            Call::create('get')->with(EncoderInterface::class)->willReturn($encoder),
        ]);

        $factory = new AcceptNegotiatorSupportedMediaTypesFactory();

        $service = $factory($container);

        self::assertSame(['application/json'], $service);
    }

    public function testCallStatic(): void
    {
        /** @var EncoderInterface $encoder */
        $encoder = $this->getMockByCalls(EncoderInterface::class, [
            Call::create('getContentTypes')->with()->willReturn(['application/json']),
        ]);

        /** @var ContainerInterface $container */
        $container = $this->getMockByCalls(ContainerInterface::class, [
            Call::create('has')->with(EncoderInterface::class.'default')->willReturn(true),
            Call::create('get')->with(EncoderInterface::class.'default')->willReturn($encoder),
        ]);

        $factory = [AcceptNegotiatorSupportedMediaTypesFactory::class, 'default'];

        $service = $factory($container);

        self::assertSame(['application/json'], $service);
    }
}
