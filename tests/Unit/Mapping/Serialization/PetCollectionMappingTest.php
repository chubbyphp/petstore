<?php

declare(strict_types=1);

namespace App\Tests\Unit\Mapping\Serialization;

use App\Collection\PetCollection;
use App\Mapping\Serialization\PetCollectionMapping;
use Chubbyphp\Serialization\Mapping\NormalizationFieldMappingBuilder;
use PHPUnit\Framework\TestCase;

/**
 * @covers \App\Mapping\Serialization\PetCollectionMapping
 */
final class PetCollectionMappingTest extends TestCase
{
    public function testGetClass()
    {
        $mapping = new PetCollectionMapping();

        self::assertSame(PetCollection::class, $mapping->getClass());
    }

    public function testGetNormalizationType()
    {
        $mapping = new PetCollectionMapping();

        self::assertSame('petCollection', $mapping->getNormalizationType());
    }

    public function testGetNormalizationFieldMappings()
    {
        $mapping = new PetCollectionMapping();

        $fieldMappings = $mapping->getNormalizationFieldMappings('/');

        self::assertEquals([
            NormalizationFieldMappingBuilder::create('offset')->getMapping(),
            NormalizationFieldMappingBuilder::create('limit')->getMapping(),
            NormalizationFieldMappingBuilder::createEmbedMany('items')->getMapping(),
        ], $fieldMappings);
    }

    public function testGetNormalizationEmbeddedFieldMappings()
    {
        $mapping = new PetCollectionMapping();

        $fieldMappings = $mapping->getNormalizationEmbeddedFieldMappings('/');

        self::assertEquals([], $fieldMappings);
    }

    public function testGetNormalizationLinkMappings()
    {
        $mapping = new PetCollectionMapping();

        $fieldMappings = $mapping->getNormalizationLinkMappings('/');

        self::assertEquals([], $fieldMappings);
    }
}
