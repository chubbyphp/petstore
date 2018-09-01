<?php

declare(strict_types=1);

namespace App\Tests\Unit\Mapping\Validation;

use App\Collection\PetCollection;
use App\Mapping\Validation\PetCollectionMapping;
use Chubbyphp\Validation\Constraint\NotBlankConstraint;
use Chubbyphp\Validation\Constraint\TypeConstraint;
use Chubbyphp\Validation\Mapping\ValidationPropertyMappingBuilder;
use PHPUnit\Framework\TestCase;

/**
 * @covers \App\Mapping\Validation\PetCollectionMapping
 */
final class PetCollectionMappingTest extends TestCase
{
    public function testGetClass()
    {
        $mapping = new PetCollectionMapping();

        self::assertSame(PetCollection::class, $mapping->getClass());
    }

    public function testGetValidationClassMapping()
    {
        $mapping = new PetCollectionMapping();

        self::assertNull($mapping->getValidationClassMapping('/path'));
    }

    public function testGetValidationPropertyMappings()
    {
        $mapping = new PetCollectionMapping();

        $propertyMappings = $mapping->getValidationPropertyMappings('/path');

        self::assertEquals([
            ValidationPropertyMappingBuilder::create('offset', [
                new NotBlankConstraint(),
                new TypeConstraint('integer'),
            ])->getMapping(),
            ValidationPropertyMappingBuilder::create('limit', [
                new NotBlankConstraint(),
                new TypeConstraint('integer'),
            ])->getMapping(),
        ], $propertyMappings);
    }
}
