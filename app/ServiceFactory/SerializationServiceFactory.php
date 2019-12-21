<?php

declare(strict_types=1);

namespace App\ServiceFactory;

use App\Collection\PetCollection;
use App\Mapping\MappingConfig;
use App\Mapping\Serialization\PetCollectionMapping;
use App\Mapping\Serialization\PetMapping;
use App\Mapping\Serialization\VaccinationMapping;
use App\Model\Pet;
use App\Model\Vaccination;
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
use Chubbyphp\Framework\Router\RouterInterface;
use Chubbyphp\Serialization\Encoder\JsonTypeEncoder;
use Chubbyphp\Serialization\Encoder\JsonxTypeEncoder;
use Chubbyphp\Serialization\Encoder\UrlEncodedTypeEncoder;
use Chubbyphp\Serialization\Encoder\YamlTypeEncoder;
use Chubbyphp\Serialization\Mapping\CallableNormalizationObjectMapping;
use Chubbyphp\Serialization\Mapping\NormalizationObjectMappingInterface;
use Psr\Container\ContainerInterface;

final class SerializationServiceFactory
{
    /**
     * @return array<string, callable>
     */
    public function __invoke(): array
    {
        return [
            'serializer.encodertypes' => static function () {
                $encoderTypes = [];

                $encoderTypes[] = new JsonTypeEncoder();
                $encoderTypes[] = new JsonxTypeEncoder(false, 'application/jsonx+xml');
                $encoderTypes[] = new UrlEncodedTypeEncoder();
                $encoderTypes[] = new YamlTypeEncoder();

                return $encoderTypes;
            },
            'serializer.mappingConfigs' => static function () {
                return [
                    BadRequest::class => new MappingConfig(BadRequestMapping::class),
                    NotAcceptable::class => new MappingConfig(NotAcceptableMapping::class),
                    NotFound::class => new MappingConfig(NotFoundMapping::class),
                    Pet::class => new MappingConfig(PetMapping::class, [RouterInterface::class]),
                    PetCollection::class => new MappingConfig(PetCollectionMapping::class, [RouterInterface::class]),
                    UnprocessableEntity::class => new MappingConfig(UnprocessableEntityMapping::class),
                    UnsupportedMediaType::class => new MappingConfig(UnsupportedMediaTypeMapping::class),
                    Vaccination::class => new MappingConfig(VaccinationMapping::class),
                ];
            },
            'serializer.normalizer.objectmappings' => function (ContainerInterface $container) {
                $mappings = [];
                foreach ($container->get('serializer.mappingConfigs') as $class => $mappingConfig) {
                    $resolver = function () use ($container, $mappingConfig) {
                        return $this->resolve($container, $mappingConfig);
                    };

                    $mappings[] = new CallableNormalizationObjectMapping($class, $resolver);
                }

                return $mappings;
            },
        ];
    }

    private function resolve(
        ContainerInterface $container,
        MappingConfig $mappingConfig
    ): NormalizationObjectMappingInterface {
        $mappingClass = $mappingConfig->getMappingClass();

        $dependencies = [];
        foreach ($mappingConfig->getDependencies() as $dependency) {
            $dependencies[] = $container->get($dependency);
        }

        return new $mappingClass(...$dependencies);
    }
}
