<?php

declare(strict_types=1);

namespace App\ServiceFactory;

use App\Mapping\Orm\PetMapping;
use App\Mapping\Orm\VaccinationMapping;
use App\Model\Pet;
use App\Model\Vaccination;

final class DoctrineOrmServiceFactory
{
    /**
     * @return array<string, callable>
     */
    public function __invoke(): array
    {
        return [
            'doctrine.orm.em.options' => static function () {
                return [
                    'mappings' => [
                        [
                            'type' => 'class_map',
                            'namespace' => 'App\Model',
                            'map' => [
                                Pet::class => PetMapping::class,
                                Vaccination::class => VaccinationMapping::class,
                            ],
                        ],
                    ],
                ];
            },
        ];
    }
}
