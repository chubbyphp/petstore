<?php

declare(strict_types=1);

namespace App\ServiceFactory;

use App\Mapping\Orm\PetMapping;
use App\Model\Pet;

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
                            ],
                        ],
                    ],
                ];
            },
        ];
    }
}
