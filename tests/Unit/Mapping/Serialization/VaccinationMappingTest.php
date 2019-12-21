<?php

declare(strict_types=1);

namespace App\Tests\Unit\Mapping\Serialization;

use App\Mapping\Serialization\VaccinationMapping;
use App\Model\Vaccination;
use Chubbyphp\Serialization\Mapping\NormalizationFieldMappingBuilder;
use PHPUnit\Framework\TestCase;

/**
 * @covers \App\Mapping\Serialization\VaccinationMapping
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

    public function testGetType(): void
    {
        $mapping = new VaccinationMapping();

        self::assertSame('vaccination', $mapping->getNormalizationType());
    }

    public function testGetNormalizationFieldMappings(): void
    {
        $mapping = new VaccinationMapping();

        $fieldMappings = $mapping->getNormalizationFieldMappings('/');

        self::assertEquals([
            NormalizationFieldMappingBuilder::create('name')->getMapping(),
        ], $fieldMappings);
    }

    public function testGetNormalizationEmbeddedFieldMappings(): void
    {
        $mapping = new VaccinationMapping();

        $embeddedFieldMappings = $mapping->getNormalizationEmbeddedFieldMappings('/');

        self::assertEquals([], $embeddedFieldMappings);
    }

    public function testGetNormalizationLinkMappings(): void
    {
        $mapping = new VaccinationMapping();

        $linkMappings = $mapping->getNormalizationLinkMappings('/');

        self::assertEquals([], $linkMappings);
    }
}
