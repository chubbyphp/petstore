<?php

declare(strict_types=1);

namespace App\ServiceProvider;

use App\Collection\PetCollection;
use App\Mapping\MappingConfig;
use App\Mapping\Serialization\PetCollectionMapping;
use App\Mapping\Serialization\PetMapping;
use App\Model\Pet;
use Chubbyphp\ApiHttp\ApiProblem\ClientError\BadRequest;
use Chubbyphp\ApiHttp\ApiProblem\ClientError\NotAcceptable;
use Chubbyphp\ApiHttp\ApiProblem\ClientError\NotFound;
use Chubbyphp\ApiHttp\ApiProblem\ClientError\UnprocessableEntity;
use Chubbyphp\ApiHttp\ApiProblem\ClientError\UnsupportedMediaType;
use Chubbyphp\ApiHttp\Serialization\ApiProblem\ClientError\BadRequestMapping;
use Chubbyphp\ApiHttp\Serialization\ApiProblem\ClientError\NotAcceptableMapping;
use Chubbyphp\ApiHttp\Serialization\ApiProblem\ClientError\NotFoundMapping;
use Chubbyphp\ApiHttp\Serialization\ApiProblem\ClientError\UnprocessableEntityMapping;
use Chubbyphp\ApiHttp\Serialization\ApiProblem\ClientError\UnsupportedMediaTypeMapping;
use Chubbyphp\Framework\Router\FastRouteRouter;
use Chubbyphp\Serialization\Encoder\JsonTypeEncoder;
use Chubbyphp\Serialization\Encoder\JsonxTypeEncoder;
use Chubbyphp\Serialization\Encoder\UrlEncodedTypeEncoder;
use Chubbyphp\Serialization\Encoder\YamlTypeEncoder;
use Chubbyphp\Serialization\Mapping\CallableNormalizationObjectMapping;
use Chubbyphp\Serialization\Mapping\NormalizationObjectMappingInterface;
use Pimple\Container;
use Pimple\ServiceProviderInterface;

final class SerializationServiceProvider implements ServiceProviderInterface
{
    public function register(Container $container): void
    {
        $container['serializer.encodertypes'] = static function () {
            $encoderTypes = [];

            $encoderTypes[] = new JsonTypeEncoder();
            $encoderTypes[] = new JsonxTypeEncoder(false, 'application/jsonx+xml');
            $encoderTypes[] = new UrlEncodedTypeEncoder();
            $encoderTypes[] = new YamlTypeEncoder();

            return $encoderTypes;
        };

        $container['serializer.mappingConfigs'] = [
            BadRequest::class => new MappingConfig(BadRequestMapping::class),
            NotAcceptable::class => new MappingConfig(NotAcceptableMapping::class),
            NotFound::class => new MappingConfig(NotFoundMapping::class),
            Pet::class => new MappingConfig(PetMapping::class, [FastRouteRouter::class]),
            PetCollection::class => new MappingConfig(PetCollectionMapping::class, [FastRouteRouter::class]),
            UnprocessableEntity::class => new MappingConfig(UnprocessableEntityMapping::class),
            UnsupportedMediaType::class => new MappingConfig(UnsupportedMediaTypeMapping::class),
        ];

        $container['serializer.normalizer.objectmappings'] = function () use ($container) {
            $mappings = [];

            foreach ($container['serializer.mappingConfigs'] as $class => $mappingConfig) {
                $resolver = function () use ($container, $mappingConfig) {
                    return $this->resolve($container, $mappingConfig);
                };

                $mappings[] = new CallableNormalizationObjectMapping($class, $resolver);
            }

            return $mappings;
        };
    }

    private function resolve(Container $container, MappingConfig $mappingConfig): NormalizationObjectMappingInterface
    {
        $mappingClass = $mappingConfig->getMappingClass();

        $dependencies = [];
        foreach ($mappingConfig->getDependencies() as $dependency) {
            $dependencies[] = $container[$dependency];
        }

        return new $mappingClass(...$dependencies);
    }
}
