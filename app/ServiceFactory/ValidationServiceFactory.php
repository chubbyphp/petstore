<?php

declare(strict_types=1);

namespace App\ServiceFactory;

use App\Collection\PetCollection;
use App\Mapping\MappingConfig;
use App\Mapping\Validation\PetCollectionMapping;
use App\Mapping\Validation\PetMapping;
use App\Mapping\Validation\VaccinationMapping;
use App\Model\Pet;
use App\Model\Vaccination;
use Chubbyphp\Validation\Mapping\CallableValidationMappingProvider;
use Chubbyphp\Validation\Mapping\ValidationMappingProviderInterface;
use Psr\Container\ContainerInterface;

final class ValidationServiceFactory
{
    /**
     * @return array<string, callable>
     */
    public function __invoke(): array
    {
        return [
            'validator.mappingConfigs' => static function () {
                return [
                    PetCollection::class => new MappingConfig(PetCollectionMapping::class),
                    Pet::class => new MappingConfig(PetMapping::class),
                    Vaccination::class => new MappingConfig(VaccinationMapping::class),
                ];
            },
            'validator.mappings' => function (ContainerInterface $container) {
                $mappings = [];
                foreach ($container->get('validator.mappingConfigs') as $class => $mappingConfig) {
                    $resolver = function () use ($container, $mappingConfig) {
                        return $this->resolve($container, $mappingConfig);
                    };

                    $mappings[] = new CallableValidationMappingProvider($class, $resolver);
                }

                return $mappings;
            },
        ];
    }

    private function resolve(
        ContainerInterface $container,
        MappingConfig $mappingConfig
    ): ValidationMappingProviderInterface {
        $mappingClass = $mappingConfig->getMappingClass();

        $dependencies = [];
        foreach ($mappingConfig->getDependencies() as $dependency) {
            $dependencies[] = $container->get($dependency);
        }

        return new $mappingClass(...$dependencies);
    }
}
