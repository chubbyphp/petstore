<?php

declare(strict_types=1);

namespace App\Tests\Unit\Repository;

use App\Model\ModelInterface;
use App\Model\Pet;
use App\Repository\PetRepository;
use App\Repository\RepositoryInterface;
use Doctrine\ORM\EntityManager;

/**
 * @covers \App\Repository\PetRepository
 */
final class PetRepositoryTest extends RepositoryTest
{
    /**
     * @return ModelInterface
     */
    protected function getModel(): ModelInterface
    {
        return new Pet();
    }

    /**
     * @param EntityManager $entityManager
     *
     * @return RepositoryInterface
     */
    protected function getRepository(EntityManager $entityManager): RepositoryInterface
    {
        return new PetRepository($entityManager);
    }
}
