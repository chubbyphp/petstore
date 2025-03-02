<?php

declare(strict_types=1);

namespace App\Tests\Unit\ServiceFactory\Negotiation;

use App\ServiceFactory\Negotiation\AcceptNegotiatorSupportedMediaTypesFactory;
use Chubbyphp\DecodeEncode\Encoder\EncoderInterface;
use Chubbyphp\Mock\MockMethod\WithReturn;
use Chubbyphp\Mock\MockObjectBuilder;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;

/**
 * @covers \App\ServiceFactory\Negotiation\AcceptNegotiatorSupportedMediaTypesFactory
 *
 * @internal
 */
final class AcceptNegotiatorSupportedMediaTypesFactoryTest extends TestCase
{
    public function testInvoke(): void
    {
        $builder = new MockObjectBuilder();

        /** @var EncoderInterface $encoder */
        $encoder = $builder->create(EncoderInterface::class, [
            new WithReturn('getContentTypes', [], ['application/json']),
        ]);

        /** @var ContainerInterface $container */
        $container = $builder->create(ContainerInterface::class, [
            new WithReturn('get', [EncoderInterface::class], $encoder),
        ]);

        $factory = new AcceptNegotiatorSupportedMediaTypesFactory();

        $service = $factory($container);

        self::assertSame(['application/json'], $service);
    }
}
