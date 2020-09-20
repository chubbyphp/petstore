<?php

declare(strict_types=1);

namespace App\ServiceFactory\Deserialization;

use App\Mapping\Deserialization\PetCollectionMapping;
use App\Mapping\Deserialization\PetMapping;
use App\Mapping\Deserialization\VaccinationMapping;
use Chubbyphp\Deserialization\Mapping\DenormalizationObjectMappingInterface;
use Psr\Container\ContainerInterface;

final class DenormalizationObjectMappingsFactory
{
    /**
     * @return array<int, DenormalizationObjectMappingInterface>
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
