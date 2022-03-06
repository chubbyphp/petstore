<?php

declare(strict_types=1);

namespace App\Repository;

use App\Collection\CollectionInterface;
use App\Collection\PetCollection;
use App\Model\ModelInterface;
use App\Model\Pet;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;

final class PetRepository implements RepositoryInterface
{
    public function __construct(private EntityManager $entityManager)
    {
    }

    /**
     * @param CollectionInterface|PetCollection $petCollection
     */
    public function resolveCollection(CollectionInterface $petCollection): void
    {
        if (!$petCollection instanceof PetCollection) {
            throw new \TypeError(
                sprintf(
                    '%s() expects parameter 1 to be %s, %s given',
                    __METHOD__,
                    PetCollection::class,
                    $petCollection::class
                )
            );
        }

        /** @var EntityRepository $entityRepository */
        $entityRepository = $this->entityManager->getRepository(Pet::class);

        $queryBuilder = $entityRepository->createQueryBuilder('p');

        $filters = $petCollection->getFilters();

        if (isset($filters['name'])) {
            $queryBuilder->andWhere($queryBuilder->expr()->like('p.name', ':name'));
            $queryBuilder->setParameter('name', '%'.$filters['name'].'%');
        }

        $countQueryBuilder = clone $queryBuilder;
        $countQueryBuilder->select($queryBuilder->expr()->count('p.id'));

        $petCollection->setCount((int) $countQueryBuilder->getQuery()->getSingleScalarResult());

        $itemsQueryBuilder = clone $queryBuilder;

        foreach ($petCollection->getSort() as $field => $order) {
            $itemsQueryBuilder->addOrderBy(sprintf('p.%s', $field), $order);
        }

        $itemsQueryBuilder->setFirstResult($petCollection->getOffset());
        $itemsQueryBuilder->setMaxResults($petCollection->getLimit());

        $petCollection->setItems($itemsQueryBuilder->getQuery()->getResult());
    }

    public function findById(string $id): ?Pet
    {
        return $this->entityManager->find(Pet::class, $id);
    }

    public function persist(ModelInterface $pet): void
    {
        if (!$pet instanceof Pet) {
            throw new \TypeError(
                sprintf(
                    '%s() expects parameter 1 to be %s, %s given',
                    __METHOD__,
                    Pet::class,
                    $pet::class
                )
            );
        }

        $this->entityManager->persist($pet);
    }

    public function remove(ModelInterface $pet): void
    {
        if (!$pet instanceof Pet) {
            throw new \TypeError(
                sprintf(
                    '%s() expects parameter 1 to be %s, %s given',
                    __METHOD__,
                    Pet::class,
                    $pet::class
                )
            );
        }

        $this->entityManager->remove($pet);
    }

    public function flush(): void
    {
        $this->entityManager->flush();
    }
}
