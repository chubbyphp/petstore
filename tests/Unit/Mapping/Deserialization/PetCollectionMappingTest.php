<?php

declare(strict_types=1);

namespace App\Tests\Unit\Mapping\Deserialization;

use App\Collection\PetCollection;
use App\Mapping\Deserialization\PetCollectionMapping;
use Chubbyphp\Deserialization\Denormalizer\ConvertTypeFieldDenormalizer;
use Chubbyphp\Deserialization\Mapping\DenormalizationFieldMappingBuilder;
use PHPUnit\Framework\TestCase;

/**
 * @covers \App\Mapping\Deserialization\PetCollectionMapping
 */
final class PetCollectionMappingTest extends TestCase
{
    public function testGetClass()
    {
        $mapping = new PetCollectionMapping();

        self::assertSame(PetCollection::class, $mapping->getClass());
    }

    public function testGetDenormalizationFactory()
    {
        $mapping = new PetCollectionMapping();

        $factory = $mapping->getDenormalizationFactory('/', 'petCollection');

        self::assertInstanceOf(\Closure::class, $factory);

        self::assertInstanceOf(PetCollection::class, $factory());
    }

    public function testGetDenormalizationFieldMappings()
    {
        $mapping = new PetCollectionMapping();

        $fieldMappings = $mapping->getDenormalizationFieldMappings('/', 'petCollection');

        self::assertEquals([
            DenormalizationFieldMappingBuilder::createConvertType('offset', ConvertTypeFieldDenormalizer::TYPE_INT)
                ->getMapping(),
            DenormalizationFieldMappingBuilder::createConvertType('limit', ConvertTypeFieldDenormalizer::TYPE_INT)
                ->getMapping(),
        ], $fieldMappings);
    }
}
