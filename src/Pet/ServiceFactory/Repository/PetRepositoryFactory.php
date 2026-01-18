<?php

declare(strict_types=1);

namespace App\Pet\ServiceFactory\Repository;

use App\Pet\Repository\PetRepository;
use Doctrine\ORM\EntityManager;
use Psr\Container\ContainerInterface;

final class PetRepositoryFactory
{
    public function __invoke(ContainerInterface $container): PetRepository
    {
        /** @var EntityManager $entityManager */
        $entityManager = $container->get(EntityManager::class);

        return new PetRepository($entityManager);
    }
}
