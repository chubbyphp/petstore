<?php

declare(strict_types=1);

namespace App\ServiceFactory\Repository;

use App\Repository\PetRepository;
use Doctrine\ORM\EntityManager;
use Psr\Container\ContainerInterface;

final class PetRepositoryFactory
{
    public function __invoke(ContainerInterface $container): PetRepository
    {
        return new PetRepository($container->get(EntityManager::class));
    }
}
