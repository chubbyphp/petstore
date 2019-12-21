<?php

declare(strict_types=1);

namespace App\ServiceFactory;

use App\Collection\PetCollection;
use App\Mapping\Deserialization\PetCollectionMapping;
use App\Mapping\Deserialization\PetMapping;
use App\Mapping\Deserialization\VaccinationMapping;
use App\Mapping\MappingConfig;
use App\Model\Pet;
use App\Model\Vaccination;
use Chubbyphp\Deserialization\Decoder\JsonTypeDecoder;
use Chubbyphp\Deserialization\Decoder\JsonxTypeDecoder;
use Chubbyphp\Deserialization\Decoder\UrlEncodedTypeDecoder;
use Chubbyphp\Deserialization\Decoder\YamlTypeDecoder;
use Chubbyphp\Deserialization\Mapping\CallableDenormalizationObjectMapping;
use Chubbyphp\Deserialization\Mapping\DenormalizationObjectMappingInterface;
use Psr\Container\ContainerInterface;

final class DeserializationServiceFactory
{
    /**
     * @return array<string, callable>
     */
    public function __invoke(): array
    {
        return [
            'deserializer.decodertypes' => static function () {
                $decoderTypes = [];

                $decoderTypes[] = new JsonTypeDecoder();
                $decoderTypes[] = new JsonxTypeDecoder('application/jsonx+xml');
                $decoderTypes[] = new UrlEncodedTypeDecoder();
                $decoderTypes[] = new YamlTypeDecoder();

                return $decoderTypes;
            },
            'deserializer.mappingConfigs' => static function () {
                return [
                    PetCollection::class => new MappingConfig(PetCollectionMapping::class),
                    Pet::class => new MappingConfig(PetMapping::class),
                    Vaccination::class => new MappingConfig(VaccinationMapping::class),
                ];
            },
            'deserializer.denormalizer.objectmappings' => function (ContainerInterface $container) {
                $mappings = [];
                foreach ($container->get('deserializer.mappingConfigs') as $class => $mappingConfig) {
                    $resolver = function () use ($container, $mappingConfig) {
                        return $this->resolve($container, $mappingConfig);
                    };

                    $mappings[] = new CallableDenormalizationObjectMapping($class, $resolver);
                }

                return $mappings;
            },
        ];
    }

    private function resolve(
        ContainerInterface $container,
        MappingConfig $mappingConfig
    ): DenormalizationObjectMappingInterface {
        $mappingClass = $mappingConfig->getMappingClass();

        $dependencies = [];
        foreach ($mappingConfig->getDependencies() as $dependency) {
            $dependencies[] = $container->get($dependency);
        }

        return new $mappingClass(...$dependencies);
    }
}
