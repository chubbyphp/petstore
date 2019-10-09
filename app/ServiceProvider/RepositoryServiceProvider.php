<?php

declare(strict_types=1);

namespace App\ServiceProvider;

use App\Repository\PetRepository;
use Pimple\Container;
use Pimple\ServiceProviderInterface;

final class RepositoryServiceProvider implements ServiceProviderInterface
{
    public function register(Container $container): void
    {
        $container[PetRepository::class] = static function () use ($container) {
            return new PetRepository($container['doctrine.orm.em']);
        };
    }
}
