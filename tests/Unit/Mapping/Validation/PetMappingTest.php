<?php

declare(strict_types=1);

namespace App\Tests\Unit\Mapping\Validation;

use App\Mapping\Validation\PetMapping;
use App\Model\Pet;
use Chubbyphp\Validation\Constraint\NotBlankConstraint;
use Chubbyphp\Validation\Constraint\NotNullConstraint;
use Chubbyphp\Validation\Constraint\TypeConstraint;
use Chubbyphp\Validation\Mapping\ValidationPropertyMappingBuilder;
use PHPUnit\Framework\TestCase;

/**
 * @covers \App\Mapping\Validation\PetMapping
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

    public function testGetValidationClassMapping(): void
    {
        $mapping = new PetMapping();

        self::assertNull($mapping->getValidationClassMapping('/path'));
    }

    public function testGetValidationPropertyMappings(): void
    {
        $mapping = new PetMapping();

        $propertyMappings = $mapping->getValidationPropertyMappings('/path');

        self::assertEquals([
            ValidationPropertyMappingBuilder::create('name', [
                new NotNullConstraint(),
                new NotBlankConstraint(),
                new TypeConstraint('string'),
            ])->getMapping(),
            ValidationPropertyMappingBuilder::create('tag', [
                new NotBlankConstraint(),
                new TypeConstraint('string'),
            ])->getMapping(),
        ], $propertyMappings);
    }
}
