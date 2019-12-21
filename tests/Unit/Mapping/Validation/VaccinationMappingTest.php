<?php

declare(strict_types=1);

namespace App\Tests\Unit\Mapping\Validation;

use App\Mapping\Validation\VaccinationMapping;
use App\Model\Vaccination;
use Chubbyphp\Validation\Constraint\NotBlankConstraint;
use Chubbyphp\Validation\Constraint\NotNullConstraint;
use Chubbyphp\Validation\Constraint\TypeConstraint;
use Chubbyphp\Validation\Mapping\ValidationPropertyMappingBuilder;
use PHPUnit\Framework\TestCase;

/**
 * @covers \App\Mapping\Validation\VaccinationMapping
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

    public function testGetValidationClassMapping(): void
    {
        $mapping = new VaccinationMapping();

        self::assertNull($mapping->getValidationClassMapping('/path'));
    }

    public function testGetValidationPropertyMappings(): void
    {
        $mapping = new VaccinationMapping();

        $propertyMappings = $mapping->getValidationPropertyMappings('/path');

        self::assertEquals([
            ValidationPropertyMappingBuilder::create('name', [
                new NotNullConstraint(),
                new NotBlankConstraint(),
                new TypeConstraint('string'),
            ])->getMapping(),
        ], $propertyMappings);
    }
}
