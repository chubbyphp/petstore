<?php

declare(strict_types=1);

namespace App\ServiceFactory\Validation;

use App\Mapping\Validation\PetCollectionMapping;
use App\Mapping\Validation\PetMapping;
use App\Mapping\Validation\VaccinationMapping;
use Chubbyphp\Validation\Mapping\ValidationMappingProviderInterface;
use Psr\Container\ContainerInterface;

final class ValidationMappingProviderFactory
{
    /**
     * @return array<int, ValidationMappingProviderInterface>
     */
    public function __invoke(ContainerInterface $container): array
    {
        return [
            new PetCollectionMapping(),
            new PetMapping(),
            new VaccinationMapping(),
        ];
    }
}
