<?php

declare(strict_types=1);

namespace App\Mapping\Validation;

use App\Collection\PetCollection;
use App\Mapping\Validation\Constraint\SortConstraint;
use Chubbyphp\Validation\Constraint\MapConstraint;
use Chubbyphp\Validation\Constraint\NotBlankConstraint;
use Chubbyphp\Validation\Constraint\TypeConstraint;
use Chubbyphp\Validation\Mapping\ValidationClassMappingInterface;
use Chubbyphp\Validation\Mapping\ValidationMappingProviderInterface;
use Chubbyphp\Validation\Mapping\ValidationPropertyMappingBuilder;
use Chubbyphp\Validation\Mapping\ValidationPropertyMappingInterface;

final class PetCollectionMapping implements ValidationMappingProviderInterface
{
    /**
     * @return string
     */
    public function getClass(): string
    {
        return PetCollection::class;
    }

    /**
     * @param string $path
     *
     * @return ValidationClassMappingInterface|null
     */
    public function getValidationClassMapping(string $path)
    {
    }

    /**
     * @param string      $path
     * @param string|null $type
     *
     * @return ValidationPropertyMappingInterface[]
     */
    public function getValidationPropertyMappings(string $path, string $type = null): array
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
            ValidationPropertyMappingBuilder::create('sort', [
                new SortConstraint(['name']),
            ])->getMapping(),
            ValidationPropertyMappingBuilder::create('filters', [
                new MapConstraint([
                    'name' => new TypeConstraint('string'),
                ]),
            ])->getMapping(),
        ];
    }
}
