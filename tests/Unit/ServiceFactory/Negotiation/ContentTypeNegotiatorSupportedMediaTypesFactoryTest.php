<?php

declare(strict_types=1);

namespace App\Tests\Unit\ServiceFactory\Negotiation;

use App\ServiceFactory\Negotiation\ContentTypeNegotiatorSupportedMediaTypesFactory;
use Chubbyphp\DecodeEncode\Decoder\DecoderInterface;
use Chubbyphp\Mock\MockMethod\WithReturn;
use Chubbyphp\Mock\MockObjectBuilder;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;

/**
 * @covers \App\ServiceFactory\Negotiation\ContentTypeNegotiatorSupportedMediaTypesFactory
 *
 * @internal
 */
final class ContentTypeNegotiatorSupportedMediaTypesFactoryTest extends TestCase
{
    public function testInvoke(): void
    {
        $builder = new MockObjectBuilder();

        /** @var DecoderInterface $decoder */
        $decoder = $builder->create(DecoderInterface::class, [
            new WithReturn('getContentTypes', [], ['application/json']),
        ]);

        /** @var ContainerInterface $container */
        $container = $builder->create(ContainerInterface::class, [
            new WithReturn('get', [DecoderInterface::class], $decoder),
        ]);

        $factory = new ContentTypeNegotiatorSupportedMediaTypesFactory();

        $service = $factory($container);

        self::assertSame(['application/json'], $service);
    }
}
