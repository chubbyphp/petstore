<?php

declare(strict_types=1);

namespace App\ServiceFactory\Deserialization;

use App\Mapping\Deserialization\PetCollectionMapping;
use App\Mapping\Deserialization\PetMapping;
use App\Mapping\Deserialization\VaccinationMapping;
use Chubbyphp\Deserialization\Mapping\DenormalizationFieldMappingFactoryInterface;
use Chubbyphp\Deserialization\Mapping\DenormalizationObjectMappingInterface;
use Chubbyphp\Deserialization\ServiceFactory\DenormalizationFieldMappingFactoryFactory;
use Chubbyphp\Laminas\Config\Factory\AbstractFactory;
use Psr\Container\ContainerInterface;

final class DenormalizationObjectMappingsFactory extends AbstractFactory
{
    /**
     * @return array<int, DenormalizationObjectMappingInterface>
     */
    public function __invoke(ContainerInterface $container): array
    {
        /** @var DenormalizationFieldMappingFactoryInterface $denormalizationFieldMappingFactory */
        $denormalizationFieldMappingFactory = $this->resolveDependency($container, DenormalizationFieldMappingFactoryInterface::class, DenormalizationFieldMappingFactoryFactory::class);

        return [
            new PetCollectionMapping($denormalizationFieldMappingFactory),
            new PetMapping($denormalizationFieldMappingFactory),
            new VaccinationMapping($denormalizationFieldMappingFactory),
        ];
    }
}
