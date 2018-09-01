<?php

declare(strict_types=1);

namespace App\ServiceProvider;

use App\Collection\PetCollection;
use App\Mapping\MappingConfig;
use App\Mapping\Serialization\PetCollectionMapping;
use App\Mapping\Serialization\PetMapping;
use App\Model\Pet;
use Chubbyphp\ApiHttp\Error\Error;
use Chubbyphp\ApiHttp\Serialization\ErrorMapping;
use Chubbyphp\Serialization\Mapping\CallableNormalizationObjectMapping;
use Pimple\Container;
use Pimple\ServiceProviderInterface;

final class SerializationServiceProvider implements ServiceProviderInterface
{
    /**
     * @param Container $container
     */
    public function register(Container $container)
    {
        $container['serializer.mappingConfigs'] = [
            Error::class => new MappingConfig(ErrorMapping::class),
            PetCollection::class => new MappingConfig(PetCollectionMapping::class),
            Pet::class => new MappingConfig(PetMapping::class),
        ];

        $container['serializer.normalizer.objectmappings'] = function () use ($container) {
            $mappings = [];

            foreach ($container['serializer.mappingConfigs'] as $class => $mappingConfig) {
                $mappingClass = $mappingConfig->getMappingClass();

                $resolver = function () use ($container, $mappingConfig) {
                    $mappingClass = $mappingConfig->getMappingClass();

                    $dependencies = [];
                    foreach ($mappingConfig->getDependencies() as $dependency) {
                        $dependencies[] = $container[$dependency];
                    }

                    return new $mappingClass(...$dependencies);
                };

                $mappings[] = new CallableNormalizationObjectMapping($class, $resolver);
            }

            return $mappings;
        };
    }
}
