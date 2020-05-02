<?php

declare(strict_types=1);

namespace App\ServiceFactory;

use App\Collection\PetCollection;
use App\Mapping\Validation\PetCollectionMapping;
use App\Mapping\Validation\PetMapping;
use App\Mapping\Validation\VaccinationMapping;
use App\Model\Pet;
use App\Model\Vaccination;
use Chubbyphp\Validation\Mapping\LazyValidationMappingProvider;
use Psr\Container\ContainerInterface;

final class ValidationServiceFactory
{
    /**
     * @return array<string, callable>
     */
    public function __invoke(): array
    {
        return [
            PetCollectionMapping::class => static function () {
                return new PetCollectionMapping();
            },
            PetMapping::class => static function () {
                return new PetMapping();
            },
            VaccinationMapping::class => static function () {
                return new VaccinationMapping();
            },
            'validator.mappings' => static function (ContainerInterface $container) {
                return [
                    new LazyValidationMappingProvider($container, PetCollectionMapping::class, PetCollection::class),
                    new LazyValidationMappingProvider($container, PetMapping::class, Pet::class),
                    new LazyValidationMappingProvider($container, VaccinationMapping::class, Vaccination::class),
                ];
            },
        ];
    }
}
