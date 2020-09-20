<?php

declare(strict_types=1);

namespace App\Mapping\Validation;

use App\Model\Vaccination;
use Chubbyphp\Validation\Constraint\NotBlankConstraint;
use Chubbyphp\Validation\Constraint\NotNullConstraint;
use Chubbyphp\Validation\Constraint\TypeConstraint;
use Chubbyphp\Validation\Mapping\ValidationClassMappingInterface;
use Chubbyphp\Validation\Mapping\ValidationMappingProviderInterface;
use Chubbyphp\Validation\Mapping\ValidationPropertyMappingBuilder;
use Chubbyphp\Validation\Mapping\ValidationPropertyMappingInterface;

final class VaccinationMapping implements ValidationMappingProviderInterface
{
    public function getClass(): string
    {
        return Vaccination::class;
    }

    public function getValidationClassMapping(string $path): ?ValidationClassMappingInterface
    {
        return null;
    }

    /**
     * @return array<ValidationPropertyMappingInterface>
     */
    public function getValidationPropertyMappings(string $path, ?string $type = null): array
    {
        return [
            ValidationPropertyMappingBuilder::create('name', [
                new NotNullConstraint(),
                new NotBlankConstraint(),
                new TypeConstraint('string'),
            ])->getMapping(),
        ];
    }
}
