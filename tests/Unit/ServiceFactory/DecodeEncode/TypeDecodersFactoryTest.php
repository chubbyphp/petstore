<?php

declare(strict_types=1);

namespace App\Tests\Unit\ServiceFactory\DecodeEncode;

use App\ServiceFactory\DecodeEncode\TypeDecodersFactory;
use Chubbyphp\DecodeEncode\Decoder\JsonTypeDecoder;
use Chubbyphp\DecodeEncode\Decoder\JsonxTypeDecoder;
use Chubbyphp\DecodeEncode\Decoder\UrlEncodedTypeDecoder;
use Chubbyphp\DecodeEncode\Decoder\YamlTypeDecoder;
use Chubbyphp\Mock\MockByCallsTrait;
use PHPUnit\Framework\TestCase;

/**
 * @covers \App\ServiceFactory\DecodeEncode\TypeDecodersFactory
 *
 * @internal
 */
final class TypeDecodersFactoryTest extends TestCase
{
    use MockByCallsTrait;

    public function testInvoke(): void
    {
        $factory = new TypeDecodersFactory();

        $typeDecoders = $factory();

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
