<?php

declare(strict_types=1);

namespace App\Tests\Unit\Mapping\Serialization;

use App\Mapping\Serialization\PetMapping;
use App\Model\Pet;
use Chubbyphp\Serialization\Mapping\NormalizationFieldMappingBuilder;
use PHPUnit\Framework\TestCase;

/**
 * @covers \App\Mapping\Serialization\PetMapping
 */
final class PetMappingTest extends TestCase
{
    public function testGetClass()
    {
        $mapping = new PetMapping();

        self::assertSame(Pet::class, $mapping->getClass());
    }

    public function testGetNormalizationType()
    {
        $mapping = new PetMapping();

        self::assertSame('pet', $mapping->getNormalizationType());
    }

    public function testGetNormalizationFieldMappings()
    {
        $mapping = new PetMapping();

        $fieldMappings = $mapping->getNormalizationFieldMappings('/');

        self::assertEquals([
            NormalizationFieldMappingBuilder::create('id')->getMapping(),
            NormalizationFieldMappingBuilder::createDateTime('createdAt', \DateTime::ATOM)->getMapping(),
            NormalizationFieldMappingBuilder::createDateTime('updatedAt', \DateTime::ATOM)->getMapping(),
            NormalizationFieldMappingBuilder::create('name')->getMapping(),
            NormalizationFieldMappingBuilder::create('tag')->getMapping(),
        ], $fieldMappings);
    }

    public function testGetNormalizationEmbeddedFieldMappings()
    {
        $mapping = new PetMapping();

        $fieldMappings = $mapping->getNormalizationEmbeddedFieldMappings('/');

        self::assertEquals([], $fieldMappings);
    }

    public function testGetNormalizationLinkMappings()
    {
        $mapping = new PetMapping();

        $fieldMappings = $mapping->getNormalizationLinkMappings('/');

        self::assertEquals([], $fieldMappings);
    }
}
