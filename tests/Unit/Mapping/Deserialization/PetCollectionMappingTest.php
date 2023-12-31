<?php

declare(strict_types=1);

namespace App\Tests\Unit\Mapping\Deserialization;

use App\Collection\PetCollection;
use App\Mapping\Deserialization\PetCollectionMapping;
use Chubbyphp\Deserialization\Denormalizer\ConvertTypeFieldDenormalizer;
use Chubbyphp\Deserialization\Mapping\DenormalizationFieldMappingFactoryInterface;
use Chubbyphp\Deserialization\Mapping\DenormalizationFieldMappingInterface;
use Chubbyphp\Mock\Call;
use Chubbyphp\Mock\MockByCallsTrait;
use PHPUnit\Framework\TestCase;

/**
 * @covers \App\Mapping\Deserialization\PetCollectionMapping
 *
 * @internal
 */
final class PetCollectionMappingTest extends TestCase
{
    use MockByCallsTrait;

    public function testGetClass(): void
    {
        /** @var DenormalizationFieldMappingFactoryInterface|MockObject $denormalizationFieldMappingFactory */
        $denormalizationFieldMappingFactory = $this->getMockByCalls(DenormalizationFieldMappingFactoryInterface::class);

        $mapping = new PetCollectionMapping($denormalizationFieldMappingFactory);

        self::assertSame(PetCollection::class, $mapping->getClass());
    }

    public function testGetDenormalizationFactory(): void
    {
        /** @var DenormalizationFieldMappingFactoryInterface|MockObject $denormalizationFieldMappingFactory */
        $denormalizationFieldMappingFactory = $this->getMockByCalls(DenormalizationFieldMappingFactoryInterface::class);

        $mapping = new PetCollectionMapping($denormalizationFieldMappingFactory);

        $factory = $mapping->getDenormalizationFactory('/', 'petCollection');

        self::assertInstanceOf(\Closure::class, $factory);

        self::assertInstanceOf(PetCollection::class, $factory());
    }

    public function testGetDenormalizationFieldMappings(): void
    {
        /** @var DenormalizationFieldMappingInterface|MockObject $offsetDenormalizationFieldMapping */
        $offsetDenormalizationFieldMapping = $this->getMockByCalls(DenormalizationFieldMappingInterface::class);

        /** @var DenormalizationFieldMappingInterface|MockObject $limitDenormalizationFieldMapping */
        $limitDenormalizationFieldMapping = $this->getMockByCalls(DenormalizationFieldMappingInterface::class);

        /** @var DenormalizationFieldMappingInterface|MockObject $filtersDenormalizationFieldMapping */
        $filtersDenormalizationFieldMapping = $this->getMockByCalls(DenormalizationFieldMappingInterface::class);

        /** @var DenormalizationFieldMappingInterface|MockObject $sortDenormalizationFieldMapping */
        $sortDenormalizationFieldMapping = $this->getMockByCalls(DenormalizationFieldMappingInterface::class);

        /** @var DenormalizationFieldMappingFactoryInterface|MockObject $denormalizationFieldMappingFactory */
        $denormalizationFieldMappingFactory = $this->getMockByCalls(DenormalizationFieldMappingFactoryInterface::class, [
            Call::create('createConvertType')->with('offset', ConvertTypeFieldDenormalizer::TYPE_INT, false, null)->willReturn($offsetDenormalizationFieldMapping),
            Call::create('createConvertType')->with('limit', ConvertTypeFieldDenormalizer::TYPE_INT, false, null)->willReturn($limitDenormalizationFieldMapping),
            Call::create('create')->with('filters', false, null, null)->willReturn($filtersDenormalizationFieldMapping),
            Call::create('create')->with('sort', false, null, null)->willReturn($sortDenormalizationFieldMapping),
        ]);

        $mapping = new PetCollectionMapping($denormalizationFieldMappingFactory);

        $fieldMappings = $mapping->getDenormalizationFieldMappings('/', 'petCollection');

        self::assertSame($offsetDenormalizationFieldMapping, array_shift($fieldMappings));
        self::assertSame($limitDenormalizationFieldMapping, array_shift($fieldMappings));
        self::assertSame($filtersDenormalizationFieldMapping, array_shift($fieldMappings));
        self::assertSame($sortDenormalizationFieldMapping, array_shift($fieldMappings));
    }
}
