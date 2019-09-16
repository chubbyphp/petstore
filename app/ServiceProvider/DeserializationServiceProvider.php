<?php

declare(strict_types=1);

namespace App\ServiceProvider;

use App\Collection\PetCollection;
use App\Mapping\Deserialization\PetCollectionMapping;
use App\Mapping\Deserialization\PetMapping;
use App\Mapping\MappingConfig;
use App\Model\Pet;
use Chubbyphp\Deserialization\Mapping\CallableDenormalizationObjectMapping;
use Chubbyphp\Deserialization\Mapping\DenormalizationObjectMappingInterface;
use Pimple\Container;
use Pimple\ServiceProviderInterface;

final class DeserializationServiceProvider implements ServiceProviderInterface
{
    /**
     * @param Container $container
     */
    public function register(Container $container): void
    {
        $container['deserializer.mappingConfigs'] = [
            PetCollection::class => new MappingConfig(PetCollectionMapping::class),
            Pet::class => new MappingConfig(PetMapping::class),
        ];

        /*
         * @return array
         */
        $container['deserializer.denormalizer.objectmappings'] = function () use ($container) {
            $mappings = [];

            foreach ($container['deserializer.mappingConfigs'] as $class => $mappingConfig) {
                $resolver = function () use ($container, $mappingConfig) {
                    return $this->resolve($container, $mappingConfig);
                };

                $mappings[] = new CallableDenormalizationObjectMapping($class, $resolver);
            }

            return $mappings;
        };
    }

    /**
     * @param Container     $container
     * @param MappingConfig $mappingConfig
     *
     * @return DenormalizationObjectMappingInterface
     */
    private function resolve(Container $container, MappingConfig $mappingConfig): DenormalizationObjectMappingInterface
    {
        $mappingClass = $mappingConfig->getMappingClass();

        $dependencies = [];
        foreach ($mappingConfig->getDependencies() as $dependency) {
            $dependencies[] = $container[$dependency];
        }

        return new $mappingClass(...$dependencies);
    }
}
