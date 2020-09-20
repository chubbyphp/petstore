<?php

declare(strict_types=1);

namespace App\Tests\Unit\ServiceFactory\Serialization;

use App\ServiceFactory\Serialization\TypeEncodersFactory;
use Chubbyphp\Mock\Call;
use Chubbyphp\Mock\MockByCallsTrait;
use Chubbyphp\Serialization\Encoder\JsonTypeEncoder;
use Chubbyphp\Serialization\Encoder\JsonxTypeEncoder;
use Chubbyphp\Serialization\Encoder\UrlEncodedTypeEncoder;
use Chubbyphp\Serialization\Encoder\YamlTypeEncoder;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;

/**
 * @covers \App\ServiceFactory\Serialization\TypeEncodersFactory
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
