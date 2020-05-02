<?php

declare(strict_types=1);

namespace App\ServiceFactory;

use App\Collection\PetCollection;
use App\Mapping\Deserialization\PetCollectionMapping;
use App\Mapping\Deserialization\PetMapping;
use App\Mapping\Deserialization\VaccinationMapping;
use App\Model\Pet;
use App\Model\Vaccination;
use Chubbyphp\Deserialization\Decoder\JsonTypeDecoder;
use Chubbyphp\Deserialization\Decoder\JsonxTypeDecoder;
use Chubbyphp\Deserialization\Decoder\UrlEncodedTypeDecoder;
use Chubbyphp\Deserialization\Decoder\YamlTypeDecoder;
use Chubbyphp\Deserialization\Mapping\LazyDenormalizationObjectMapping;
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
                return [
                    new JsonTypeDecoder(),
                    new JsonxTypeDecoder('application/jsonx+xml'),
                    new UrlEncodedTypeDecoder(),
                    new YamlTypeDecoder(),
                ];
            },
            PetCollectionMapping::class => static function () {
                return new PetCollectionMapping();
            },
            PetMapping::class => static function () {
                return new PetMapping();
            },
            VaccinationMapping::class => static function () {
                return new VaccinationMapping();
            },
            'deserializer.denormalizer.objectmappings' => static function (ContainerInterface $container) {
                return [
                    new LazyDenormalizationObjectMapping($container, PetCollectionMapping::class, PetCollection::class),
                    new LazyDenormalizationObjectMapping($container, PetMapping::class, Pet::class),
                    new LazyDenormalizationObjectMapping($container, VaccinationMapping::class, Vaccination::class),
                ];
            },
        ];
    }
}
