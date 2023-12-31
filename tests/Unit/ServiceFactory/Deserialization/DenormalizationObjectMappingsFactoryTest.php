<?php

declare(strict_types=1);

namespace App\Tests\Unit\ServiceFactory\Deserialization;

use App\ServiceFactory\Deserialization\DenormalizationObjectMappingsFactory;
use Chubbyphp\Deserialization\Mapping\DenormalizationFieldMappingFactoryInterface;
use Chubbyphp\Mock\Call;
use Chubbyphp\Mock\MockByCallsTrait;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;

/**
 * @covers \App\ServiceFactory\Deserialization\DenormalizationObjectMappingsFactory
 *
 * @internal
 */
final class DenormalizationObjectMappingsFactoryTest extends TestCase
{
    use MockByCallsTrait;

    public function testInvoke(): void
    {
        /** @var DenormalizationFieldMappingFactoryInterface|MockObject $denormalizationFieldMappingFactory */
        $denormalizationFieldMappingFactory = $this->getMockByCalls(DenormalizationFieldMappingFactoryInterface::class);

        /** @var ContainerInterface $container */
        $container = $this->getMockByCalls(ContainerInterface::class, [
            Call::create('has')->with(DenormalizationFieldMappingFactoryInterface::class)->willReturn(true),
            Call::create('get')->with(DenormalizationFieldMappingFactoryInterface::class)->willReturn($denormalizationFieldMappingFactory),
        ]);

        $factory = new DenormalizationObjectMappingsFactory();
        $factory($container);
    }
}
