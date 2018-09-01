<?php

declare(strict_types=1);

namespace App\ServiceProvider;

use App\Collection\PetCollection;
use App\Mapping\MappingConfig;
use App\Mapping\Validation\PetCollectionMapping;
use App\Mapping\Validation\PetMapping;
use App\Model\Pet;
use Chubbyphp\Validation\Mapping\CallableValidationMappingProvider;
use Pimple\Container;
use Pimple\ServiceProviderInterface;

final class ValidationServiceProvider implements ServiceProviderInterface
{
    /**
     * @param Container $container
     */
    public function register(Container $container)
    {
        $container['validator.mappingConfigs'] = [
            PetCollection::class => new MappingConfig(PetCollectionMapping::class),
            Pet::class => new MappingConfig(PetMapping::class),
        ];

        $container['validator.mappings'] = function () use ($container) {
            $mappings = [];

            foreach ($container['validator.mappingConfigs'] as $class => $mappingConfig) {
                $mappingClass = $mappingConfig->getMappingClass();

                $resolver = function () use ($container, $mappingConfig) {
                    $mappingClass = $mappingConfig->getMappingClass();

                    $dependencies = [];
                    foreach ($mappingConfig->getDependencies() as $dependency) {
                        $dependencies[] = $container[$dependency];
                    }

                    return new $mappingClass(...$dependencies);
                };

                $mappings[] = new CallableValidationMappingProvider($class, $resolver);
            }

            return $mappings;
        };
    }
}
