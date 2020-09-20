<?php

declare(strict_types=1);

namespace App\Tests\Unit\ServiceFactory\Deserialization;

use App\ServiceFactory\Deserialization\TypeDecodersFactory;
use Chubbyphp\Deserialization\Decoder\JsonTypeDecoder;
use Chubbyphp\Deserialization\Decoder\JsonxTypeDecoder;
use Chubbyphp\Deserialization\Decoder\UrlEncodedTypeDecoder;
use Chubbyphp\Deserialization\Decoder\YamlTypeDecoder;
use Chubbyphp\Mock\MockByCallsTrait;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;

/**
 * @covers \App\ServiceFactory\Deserialization\TypeDecodersFactory
 *
 * @internal
 */
final class TypeDecodersFactoryTest extends TestCase
{
    use MockByCallsTrait;

    public function testInvoke(): void
    {
        /** @var ContainerInterface $container */
        $container = $this->getMockByCalls(ContainerInterface::class);

        $factory = new TypeDecodersFactory();

        $typeDecoders = $factory($container);

        self::assertIsArray($typeDecoders);

        self::assertCount(4, $typeDecoders);

        self::assertInstanceOf(JsonTypeDecoder::class, array_shift($typeDecoders));

        /** @var JsonxTypeDecoder $jsonxTypeDecoder */
        $jsonxTypeDecoder = array_shift($typeDecoders);
        self::assertInstanceOf(JsonxTypeDecoder::class, $jsonxTypeDecoder);

        self::assertSame('application/jsonx+xml', $jsonxTypeDecoder->getContentType());

        self::assertInstanceOf(UrlEncodedTypeDecoder::class, array_shift($typeDecoders));
        self::assertInstanceOf(YamlTypeDecoder::class, array_shift($typeDecoders));
    }
}
