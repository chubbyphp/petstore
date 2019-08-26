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

    /**
     * @param EntityManager $entityManager
     */
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

        $qb = $entityRepository->createQueryBuilder('p');

        $filters = $petCollection->getFilters();

        if (isset($filters['name'])) {
            $qb->andWhere($qb->expr()->like('p.name', ':name'));
            $qb->setParameter('name', '%'.$filters['name'].'%');
        }

        $countQb = clone $qb;
        $countQb->select($qb->expr()->count('p.id'));

        $petCollection->setCount((int) $countQb->getQuery()->getSingleScalarResult());

        $itemsQb = clone $qb;

        foreach ($petCollection->getSort() as $field => $order) {
            $itemsQb->addOrderBy(sprintf('p.%s', $field), $order);
        }

        $itemsQb->setFirstResult($petCollection->getOffset());
        $itemsQb->setMaxResults($petCollection->getLimit());

        $petCollection->setItems($itemsQb->getQuery()->getResult());
    }

    /**
     * @param string $id
     *
     * @return Pet|ModelInterface|null
     */
    public function findById(string $id): ?ModelInterface
    {
        /** @var Pet|ModelInterface|null */
        $pet = $this->entityManager->find(Pet::class, $id);

        return $pet;
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
