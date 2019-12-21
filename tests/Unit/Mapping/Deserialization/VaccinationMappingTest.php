<?php

declare(strict_types=1);

namespace App\Tests\Unit\Mapping\Deserialization;

use App\Mapping\Deserialization\VaccinationMapping;
use App\Model\Vaccination;
use Chubbyphp\Deserialization\Denormalizer\ConvertTypeFieldDenormalizer;
use Chubbyphp\Deserialization\Mapping\DenormalizationFieldMappingBuilder;
use PHPUnit\Framework\TestCase;

/**
 * @covers \App\Mapping\Deserialization\VaccinationMapping
 *
 * @internal
 */
final class VaccinationMappingTest extends TestCase
{
    public function testGetClass(): void
    {
        $mapping = new VaccinationMapping();

        self::assertSame(Vaccination::class, $mapping->getClass());
    }

    public function testGetDenormalizationFactory(): void
    {
        $mapping = new VaccinationMapping();

        $factory = $mapping->getDenormalizationFactory('/', 'vaccination');

        self::assertInstanceOf(\Closure::class, $factory);

        self::assertInstanceOf(Vaccination::class, $factory());
    }

    public function testGetDenormalizationFieldMappings(): void
    {
        $mapping = new VaccinationMapping();

        $fieldMappings = $mapping->getDenormalizationFieldMappings('/', 'vaccination');

        self::assertEquals([
            DenormalizationFieldMappingBuilder::createConvertType('name', ConvertTypeFieldDenormalizer::TYPE_STRING)
                ->getMapping(),
        ], $fieldMappings);
    }
}
