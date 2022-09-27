<?php

declare(strict_types=1);

namespace App\ServiceFactory\Serialization;

use App\Mapping\Serialization\PetCollectionMapping;
use App\Mapping\Serialization\PetMapping;
use App\Mapping\Serialization\VaccinationMapping;
use Chubbyphp\Serialization\Mapping\NormalizationObjectMappingInterface;
use Psr\Container\ContainerInterface;
use Slim\Interfaces\RouteParserInterface;

final class NormalizationObjectMappingsFactory
{
    /**
     * @return array<int, NormalizationObjectMappingInterface>
     */
    public function __invoke(ContainerInterface $container): array
    {
        /** @var RouteParserInterface $router */
        $router = $container->get(RouteParserInterface::class);

        return [
            new PetCollectionMapping($router),
            new PetMapping($router),
            new VaccinationMapping(),
        ];
    }
}
