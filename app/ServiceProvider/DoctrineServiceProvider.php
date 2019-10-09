<?php

declare(strict_types=1);

namespace App\ServiceProvider;

use App\Mapping\Orm\PetMapping;
use App\Model\Pet;
use Pimple\Container;
use Pimple\ServiceProviderInterface;

final class DoctrineServiceProvider implements ServiceProviderInterface
{
    public function register(Container $container): void
    {
        $container['doctrine.orm.em.options'] = [
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
    }
}
