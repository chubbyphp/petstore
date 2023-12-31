<?php

declare(strict_types=1);

namespace App\Tests\Unit\Mapping\Deserialization;

use App\Mapping\Deserialization\VaccinationMapping;
use App\Model\Vaccination;
use Chubbyphp\Deserialization\Denormalizer\ConvertTypeFieldDenormalizer;
use Chubbyphp\Deserialization\Mapping\DenormalizationFieldMappingFactoryInterface;
use Chubbyphp\Deserialization\Mapping\DenormalizationFieldMappingInterface;
use Chubbyphp\Mock\Call;
use Chubbyphp\Mock\MockByCallsTrait;
use PHPUnit\Framework\TestCase;

/**
 * @covers \App\Mapping\Deserialization\VaccinationMapping
 *
 * @internal
 */
final class VaccinationMappingTest extends TestCase
{
    use MockByCallsTrait;

    public function testGetClass(): void
    {
        /** @var DenormalizationFieldMappingFactoryInterface|MockObject $denormalizationFieldMappingFactory */
        $denormalizationFieldMappingFactory = $this->getMockByCalls(DenormalizationFieldMappingFactoryInterface::class);

        $mapping = new VaccinationMapping($denormalizationFieldMappingFactory);

        self::assertSame(Vaccination::class, $mapping->getClass());
    }

    public function testGetDenormalizationFactory(): void
    {
        /** @var DenormalizationFieldMappingFactoryInterface|MockObject $denormalizationFieldMappingFactory */
        $denormalizationFieldMappingFactory = $this->getMockByCalls(DenormalizationFieldMappingFactoryInterface::class);

        $mapping = new VaccinationMapping($denormalizationFieldMappingFactory);

        $factory = $mapping->getDenormalizationFactory('/', 'vaccination');

        self::assertInstanceOf(\Closure::class, $factory);

        self::assertInstanceOf(Vaccination::class, $factory());
    }

    public function testGetDenormalizationFieldMappings(): void
    {
        /** @var DenormalizationFieldMappingInterface|MockObject $nameDenormalizationFieldMapping */
        $nameDenormalizationFieldMapping = $this->getMockByCalls(DenormalizationFieldMappingInterface::class);

        /** @var DenormalizationFieldMappingFactoryInterface|MockObject $denormalizationFieldMappingFactory */
        $denormalizationFieldMappingFactory = $this->getMockByCalls(DenormalizationFieldMappingFactoryInterface::class, [
            Call::create('createConvertType')->with('name', ConvertTypeFieldDenormalizer::TYPE_STRING, false, null)->willReturn($nameDenormalizationFieldMapping),
        ]);

        $mapping = new VaccinationMapping($denormalizationFieldMappingFactory);
        $mapping->getDenormalizationFieldMappings('/', 'vaccination');
    }
}
