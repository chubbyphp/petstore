<?php

declare(strict_types=1);

namespace App\Mapping\Validation;

use App\Collection\PetCollection;
use Chubbyphp\Validation\Constraint\MapConstraint;
use Chubbyphp\Validation\Constraint\NotBlankConstraint;
use Chubbyphp\Validation\Constraint\SortConstraint;
use Chubbyphp\Validation\Constraint\TypeConstraint;
use Chubbyphp\Validation\Mapping\ValidationClassMappingInterface;
use Chubbyphp\Validation\Mapping\ValidationMappingProviderInterface;
use Chubbyphp\Validation\Mapping\ValidationPropertyMappingBuilder;
use Chubbyphp\Validation\Mapping\ValidationPropertyMappingInterface;

final class PetCollectionMapping implements ValidationMappingProviderInterface
{
    public function getClass(): string
    {
        return PetCollection::class;
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
            ValidationPropertyMappingBuilder::create('offset', [
                new NotBlankConstraint(),
                new TypeConstraint('integer'),
            ])->getMapping(),
            ValidationPropertyMappingBuilder::create('limit', [
                new NotBlankConstraint(),
                new TypeConstraint('integer'),
            ])->getMapping(),
            ValidationPropertyMappingBuilder::create('filters', [
                new MapConstraint([
                    'name' => [
                        new NotBlankConstraint(),
                        new TypeConstraint('string'),
                    ],
                ]),
            ])->getMapping(),
            ValidationPropertyMappingBuilder::create('sort', [
                new SortConstraint(['name']),
            ])->getMapping(),
        ];
    }
}
