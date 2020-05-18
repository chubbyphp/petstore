<?php

declare(strict_types=1);

namespace App\Mapping\Validation;

use App\Model\User;
use Chubbyphp\Validation\Constraint\NotBlankConstraint;
use Chubbyphp\Validation\Constraint\NotNullConstraint;
use Chubbyphp\Validation\Constraint\TypeConstraint;
use Chubbyphp\Validation\Mapping\ValidationClassMappingInterface;
use Chubbyphp\Validation\Mapping\ValidationMappingProviderInterface;
use Chubbyphp\Validation\Mapping\ValidationPropertyMappingBuilder;
use Chubbyphp\Validation\Mapping\ValidationPropertyMappingInterface;

final class UserMapping implements ValidationMappingProviderInterface
{
    public function getClass(): string
    {
        return User::class;
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
            ValidationPropertyMappingBuilder::create('username', [
                new NotNullConstraint(),
                new NotBlankConstraint(),
                new TypeConstraint('string'),
            ])->getMapping(),
            ValidationPropertyMappingBuilder::create('password', [
                new NotNullConstraint(),
                new NotBlankConstraint(),
                new TypeConstraint('string'),
            ])->getMapping(),
        ];
    }
}
