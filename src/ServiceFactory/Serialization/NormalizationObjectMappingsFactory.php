<?php

declare(strict_types=1);

namespace App\ServiceFactory\Serialization;

use App\Mapping\Serialization\PetCollectionMapping;
use App\Mapping\Serialization\PetMapping;
use App\Mapping\Serialization\VaccinationMapping;
use Chubbyphp\Framework\Router\UrlGeneratorInterface;
use Chubbyphp\Serialization\Mapping\NormalizationObjectMappingInterface;
use Psr\Container\ContainerInterface;

final class NormalizationObjectMappingsFactory
{
    /**
     * @return array<int, NormalizationObjectMappingInterface>
     */
    public function __invoke(ContainerInterface $container): array
    {
        /** @var UrlGeneratorInterface $urlGenerator */
        $urlGenerator = $container->get(UrlGeneratorInterface::class);

        return [
            new PetCollectionMapping($urlGenerator),
            new PetMapping($urlGenerator),
            new VaccinationMapping(),
        ];
    }
}
