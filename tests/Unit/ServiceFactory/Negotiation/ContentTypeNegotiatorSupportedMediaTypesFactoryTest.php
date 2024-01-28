<?php

declare(strict_types=1);

namespace App\Tests\Unit\ServiceFactory\Negotiation;

use App\ServiceFactory\Negotiation\ContentTypeNegotiatorSupportedMediaTypesFactory;
use Chubbyphp\DecodeEncode\Decoder\DecoderInterface;
use Chubbyphp\Mock\Call;
use Chubbyphp\Mock\MockByCallsTrait;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;

/**
 * @covers \App\ServiceFactory\Negotiation\ContentTypeNegotiatorSupportedMediaTypesFactory
 *
 * @internal
 */
final class ContentTypeNegotiatorSupportedMediaTypesFactoryTest extends TestCase
{
    use MockByCallsTrait;

    public function testInvoke(): void
    {
        /** @var DecoderInterface $decoder */
        $decoder = $this->getMockByCalls(DecoderInterface::class, [
            Call::create('getContentTypes')->with()->willReturn(['application/json']),
        ]);

        /** @var ContainerInterface $container */
        $container = $this->getMockByCalls(ContainerInterface::class, [
            Call::create('get')->with(DecoderInterface::class)->willReturn($decoder),
        ]);

        $factory = new ContentTypeNegotiatorSupportedMediaTypesFactory();

        $service = $factory($container);

        self::assertSame(['application/json'], $service);
    }
}
