<?php

declare(strict_types=1);

namespace App\Tests\Unit\Mapping\Deserialization;

use App\Mapping\Deserialization\PetMapping;
use App\Model\Pet;
use Chubbyphp\Deserialization\Denormalizer\ConvertTypeFieldDenormalizer;
use Chubbyphp\Deserialization\Denormalizer\Relation\EmbedManyFieldDenormalizer;
use Chubbyphp\Deserialization\Mapping\DenormalizationFieldMappingFactoryInterface;
use Chubbyphp\Deserialization\Mapping\DenormalizationFieldMappingInterface;
use Chubbyphp\Mock\Argument\ArgumentInstanceOf;
use Chubbyphp\Mock\Call;
use Chubbyphp\Mock\MockByCallsTrait;
use PHPUnit\Framework\TestCase;

/**
 * @covers \App\Mapping\Deserialization\PetMapping
 *
 * @internal
 */
final class PetMappingTest extends TestCase
{
    use MockByCallsTrait;

    public function testGetClass(): void
    {
        /** @var DenormalizationFieldMappingFactoryInterface|MockObject $denormalizationFieldMappingFactory */
        $denormalizationFieldMappingFactory = $this->getMockByCalls(DenormalizationFieldMappingFactoryInterface::class);

        $mapping = new PetMapping($denormalizationFieldMappingFactory);

        self::assertSame(Pet::class, $mapping->getClass());
    }

    public function testGetDenormalizationFactory(): void
    {
        /** @var DenormalizationFieldMappingFactoryInterface|MockObject $denormalizationFieldMappingFactory */
        $denormalizationFieldMappingFactory = $this->getMockByCalls(DenormalizationFieldMappingFactoryInterface::class);

        $mapping = new PetMapping($denormalizationFieldMappingFactory);

        $factory = $mapping->getDenormalizationFactory('/', 'pet');

        self::assertInstanceOf(\Closure::class, $factory);

        self::assertInstanceOf(Pet::class, $factory());
    }

    public function testGetDenormalizationFieldMappings(): void
    {
        /** @var DenormalizationFieldMappingInterface|MockObject $nameDenormalizationFieldMapping */
        $nameDenormalizationFieldMapping = $this->getMockByCalls(DenormalizationFieldMappingInterface::class);

        /** @var DenormalizationFieldMappingInterface|MockObject $tagDenormalizationFieldMapping */
        $tagDenormalizationFieldMapping = $this->getMockByCalls(DenormalizationFieldMappingInterface::class);

        /** @var DenormalizationFieldMappingInterface|MockObject $vaccinationsDenormalizationFieldMapping */
        $vaccinationsDenormalizationFieldMapping = $this->getMockByCalls(DenormalizationFieldMappingInterface::class);

        /** @var DenormalizationFieldMappingFactoryInterface|MockObject $denormalizationFieldMappingFactory */
        $denormalizationFieldMappingFactory = $this->getMockByCalls(DenormalizationFieldMappingFactoryInterface::class, [
            Call::create('createConvertType')->with('name', ConvertTypeFieldDenormalizer::TYPE_STRING, false, null)->willReturn($nameDenormalizationFieldMapping),
            Call::create('createConvertType')->with('tag', ConvertTypeFieldDenormalizer::TYPE_STRING, false, null)->willReturn($tagDenormalizationFieldMapping),
            Call::create('create')->with('vaccinations', false, new ArgumentInstanceOf(EmbedManyFieldDenormalizer::class), null)->willReturn($vaccinationsDenormalizationFieldMapping),
        ]);

        $mapping = new PetMapping($denormalizationFieldMappingFactory);
        $mapping->getDenormalizationFieldMappings('/', 'pet');
    }
}
