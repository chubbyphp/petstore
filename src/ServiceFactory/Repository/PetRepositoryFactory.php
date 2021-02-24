<?php

declare(strict_types=1);

namespace App\ServiceFactory\Repository;

use App\Repository\PetRepository;
use Doctrine\ODM\MongoDB\DocumentManager;
use Psr\Container\ContainerInterface;

final class PetRepositoryFactory
{
    public function __invoke(ContainerInterface $container): PetRepository
    {
        return new PetRepository($container->get(DocumentManager::class));
    }
}
