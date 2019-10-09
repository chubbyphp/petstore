<?php

declare(strict_types=1);

namespace App\ServiceProvider;

use App\Factory\Collection\PetCollectionFactory;
use App\Factory\Model\PetFactory;
use Pimple\Container;
use Pimple\ServiceProviderInterface;

final class FactoryServiceProvider implements ServiceProviderInterface
{
    public function register(Container $container): void
    {
        $container[PetCollectionFactory::class] = static function () {
            return new PetCollectionFactory();
        };

        $container[PetFactory::class] = static function () {
            return new PetFactory();
        };
    }
}
