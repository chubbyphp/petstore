<?php

declare(strict_types=1);

namespace App\Tests\Unit\ServiceFactory\DecodeEncode;

use App\ServiceFactory\DecodeEncode\TypeEncodersFactory;
use Chubbyphp\DecodeEncode\Encoder\JsonTypeEncoder;
use Chubbyphp\DecodeEncode\Encoder\JsonxTypeEncoder;
use Chubbyphp\DecodeEncode\Encoder\UrlEncodedTypeEncoder;
use Chubbyphp\DecodeEncode\Encoder\YamlTypeEncoder;
use Chubbyphp\Mock\Call;
use Chubbyphp\Mock\MockByCallsTrait;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;

/**
 * @covers \App\ServiceFactory\DecodeEncode\TypeEncodersFactory
 *
 * @internal
 */
final class TypeEncodersFactoryTest extends TestCase
{
    use MockByCallsTrait;

    public function testInvoke(): void
    {
        /** @var ContainerInterface $container */
        $container = $this->getMockByCalls(ContainerInterface::class, [
            Call::create('get')->with('config')->willReturn(['debug' => true]),
        ]);

        $factory = new TypeEncodersFactory();

        $typeEncoders = $factory($container);

        self::assertIsArray($typeEncoders);

        self::assertCount(4, $typeEncoders);

        self::assertInstanceOf(JsonTypeEncoder::class, array_shift($typeEncoders));

        /** @var JsonxTypeEncoder $jsonxTypeEncoder */
        $jsonxTypeEncoder = array_shift($typeEncoders);
        self::assertInstanceOf(JsonxTypeEncoder::class, $jsonxTypeEncoder);

        self::assertSame('application/jsonx+xml', $jsonxTypeEncoder->getContentType());

        self::assertInstanceOf(UrlEncodedTypeEncoder::class, array_shift($typeEncoders));
        self::assertInstanceOf(YamlTypeEncoder::class, array_shift($typeEncoders));
    }
}
