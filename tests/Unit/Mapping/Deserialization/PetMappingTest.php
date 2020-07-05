<?php

declare(strict_types=1);

namespace App\Tests\Unit\Mapping\Deserialization;

use App\Mapping\Deserialization\PetMapping;
use App\Model\Pet;
use App\Model\Vaccination;
use Chubbyphp\Deserialization\Accessor\MethodAccessor;
use Chubbyphp\Deserialization\Denormalizer\ConvertTypeFieldDenormalizer;
use Chubbyphp\Deserialization\Denormalizer\Relation\EmbedManyFieldDenormalizer;
use Chubbyphp\Deserialization\Mapping\DenormalizationFieldMappingBuilder;
use PHPUnit\Framework\TestCase;

/**
 * @covers \App\Mapping\Deserialization\PetMapping
 *
 * @internal
 */
final class PetMappingTest extends TestCase
{
    public function testGetClass(): void
    {
        $mapping = new PetMapping();

        self::assertSame(Pet::class, $mapping->getClass());
    }

    public function testGetDenormalizationFactory(): void
    {
        $mapping = new PetMapping();

        $factory = $mapping->getDenormalizationFactory('/', 'pet');

        self::assertInstanceOf(\Closure::class, $factory);

        self::assertInstanceOf(Pet::class, $factory());
    }

    public function testGetDenormalizationFieldMappings(): void
    {
        $mapping = new PetMapping();

        $fieldMappings = $mapping->getDenormalizationFieldMappings('/', 'pet');

        self::assertEquals([
            DenormalizationFieldMappingBuilder::createConvertType('name', ConvertTypeFieldDenormalizer::TYPE_STRING)
                ->getMapping(),
            DenormalizationFieldMappingBuilder::createConvertType('tag', ConvertTypeFieldDenormalizer::TYPE_STRING)
                ->getMapping(),
            DenormalizationFieldMappingBuilder::create(
                'vaccinations',
                false,
                new EmbedManyFieldDenormalizer(Vaccination::class, new MethodAccessor('vaccinations'))
            )->getMapping(),
        ], $fieldMappings);
    }
}
