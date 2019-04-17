<?php

declare(strict_types=1);

namespace App\ServiceProvider;

use App\Collection\PetCollection;
use App\Mapping\MappingConfig;
use App\Mapping\Serialization\PetCollectionMapping;
use App\Mapping\Serialization\PetMapping;
use App\Model\Pet;
use Chubbyphp\Serialization\Mapping\CallableNormalizationObjectMapping;
use Pimple\Container;
use Pimple\ServiceProviderInterface;
use Chubbyphp\ApiHttp\ApiProblem\ClientError\UnprocessableEntity;
use Chubbyphp\ApiHttp\Serialization\ApiProblem\ClientError\UnprocessableEntityMapping;
use Chubbyphp\ApiHttp\ApiProblem\ClientError\NotFound;
use Chubbyphp\ApiHttp\Serialization\ApiProblem\ClientError\NotFoundMapping;
use Chubbyphp\ApiHttp\ApiProblem\ClientError\NotAcceptable;
use Chubbyphp\ApiHttp\Serialization\ApiProblem\ClientError\NotAcceptableMapping;
use Chubbyphp\ApiHttp\ApiProblem\ClientError\UnsupportedMediaType;
use Chubbyphp\ApiHttp\Serialization\ApiProblem\ClientError\UnsupportedMediaTypeMapping;

final class SerializationServiceProvider implements ServiceProviderInterface
{
    /**
     * @param Container $container
     */
    public function register(Container $container): void
    {
        $container['serializer.mappingConfigs'] = [
            NotAcceptable::class => new MappingConfig(NotAcceptableMapping::class),
            NotFound::class => new MappingConfig(NotFoundMapping::class),
            UnprocessableEntity::class => new MappingConfig(UnprocessableEntityMapping::class),
            UnsupportedMediaType::class => new MappingConfig(UnsupportedMediaTypeMapping::class),
            PetCollection::class => new MappingConfig(PetCollectionMapping::class, ['router']),
            Pet::class => new MappingConfig(PetMapping::class, ['router']),
        ];

        $container['serializer.normalizer.objectmappings'] = function () use ($container) {
            $mappings = [];

            foreach ($container['serializer.mappingConfigs'] as $class => $mappingConfig) {
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
