<?php

declare(strict_types=1);

namespace App\ServiceFactory\Validation;

use App\Mapping\Validation\PetCollectionMapping;
use App\Mapping\Validation\PetMapping;
use App\Mapping\Validation\VaccinationMapping;
use Chubbyphp\Validation\Mapping\ValidationMappingProviderInterface;

final class ValidationMappingProviderFactory
{
    /**
     * @return array<int, ValidationMappingProviderInterface>
     */
    public function __invoke(): array
    {
        return [
            new PetCollectionMapping(),
            new PetMapping(),
            new VaccinationMapping(),
        ];
    }
}
