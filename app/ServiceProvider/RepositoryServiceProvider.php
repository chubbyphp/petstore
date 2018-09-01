<?php

declare(strict_types=1);

namespace App\ServiceProvider;

use App\Model\Pet;
use App\Repository\Repository;
use Pimple\Container;
use Pimple\ServiceProviderInterface;

final class RepositoryServiceProvider implements ServiceProviderInterface
{
    /**
     * @param Container $container
     */
    public function register(Container $container)
    {
        $container[Repository::class.Pet::class] = function () use ($container) {
            return new Repository($container['doctrine.orm.em'], Pet::class);
        };
    }
}
