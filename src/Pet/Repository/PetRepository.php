<?php

declare(strict_types=1);

namespace App\Pet\Repository;

use App\Pet\Collection\PetCollection;
use App\Pet\Model\Pet;
use Chubbyphp\Api\Collection\CollectionInterface;
use Chubbyphp\Api\Model\ModelInterface;
use Chubbyphp\Api\Repository\RepositoryInterface;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;

final class PetRepository implements RepositoryInterface
{
    public function __construct(private readonly EntityManager $entityManager) {}

    /**
     * @param CollectionInterface|PetCollection $petCollection
     */
    public function resolveCollection(CollectionInterface $petCollection): void
    {
        if (!$petCollection instanceof PetCollection) {
            throw new \TypeError(
                \sprintf(
                    '%s() expects parameter 1 to be %s, %s given',
                    __METHOD__,
                    PetCollection::class,
                    $petCollection::class
                )
            );
        }

        /** @var EntityRepository<Pet> $entityRepository */
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
            $itemsQueryBuilder->addOrderBy(\sprintf('p.%s', $field), $order);
        }

        $itemsQueryBuilder->setFirstResult($petCollection->getOffset());
        $itemsQueryBuilder->setMaxResults($petCollection->getLimit());

        /** @var array<Pet> $items */
        $items = $itemsQueryBuilder->getQuery()->getResult();
        $petCollection->setItems($items);
    }

    public function findById(string $id): ?Pet
    {
        return $this->entityManager->find(Pet::class, $id);
    }

    public function persist(ModelInterface $pet): void
    {
        if (!$pet instanceof Pet) {
            throw new \TypeError(
                \sprintf(
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
                \sprintf(
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
