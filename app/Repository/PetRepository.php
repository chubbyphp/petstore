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
    /**
     * @var EntityManager
     */
    private $entityManager;

    public function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * @param PetCollection|CollectionInterface $petCollection
     */
    public function resolveCollection(CollectionInterface $petCollection): void
    {
        if (!$petCollection instanceof PetCollection) {
            throw new \TypeError(
                sprintf(
                    '%s() expects parameter 1 to be %s, %s given',
                    __METHOD__,
                    PetCollection::class,
                    get_class($petCollection)
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

    /**
     * @return Pet|ModelInterface|null
     */
    public function findById(string $id): ?ModelInterface
    {
        return $this->entityManager->find(Pet::class, $id);
    }

    /**
     * @param Pet|ModelInterface $pet
     */
    public function persist(ModelInterface $pet): void
    {
        if (!$pet instanceof Pet) {
            throw new \TypeError(
                sprintf(
                    '%s() expects parameter 1 to be %s, %s given',
                    __METHOD__,
                    Pet::class,
                    get_class($pet)
                )
            );
        }

        $this->entityManager->persist($pet);
    }

    /**
     * @param Pet|ModelInterface $pet
     */
    public function remove(ModelInterface $pet): void
    {
        if (!$pet instanceof Pet) {
            throw new \TypeError(
                sprintf(
                    '%s() expects parameter 1 to be %s, %s given',
                    __METHOD__,
                    Pet::class,
                    get_class($pet)
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
