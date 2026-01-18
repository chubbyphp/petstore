<?php

declare(strict_types=1);

namespace App\Pet\Repository;

use App\Pet\Collection\PetCollection;
use App\Pet\Model\Pet;
use Chubbyphp\Api\Collection\CollectionInterface;
use Chubbyphp\Api\Model\ModelInterface;
use Chubbyphp\Api\Repository\RepositoryInterface;
use Doctrine\ODM\MongoDB\DocumentManager;
use Doctrine\ODM\MongoDB\Iterator\Iterator;
use Doctrine\ODM\MongoDB\Repository\DocumentRepository;

final class PetRepository implements RepositoryInterface
{
    public function __construct(private readonly DocumentManager $documentManager) {}

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

        /** @var DocumentRepository<Pet> $documentRepository */
        $documentRepository = $this->documentManager->getRepository(Pet::class);

        $queryBuilder = $documentRepository->createQueryBuilder();

        $filters = $petCollection->getFilters();

        if (isset($filters['name'])) {
            $queryBuilder->field('name')->text($filters['name']);
        }

        $countQueryBuilder = clone $queryBuilder;
        $countQueryBuilder->count();

        /** @var int $count */
        $count = $countQueryBuilder->getQuery()->execute();
        $petCollection->setCount($count);

        $itemsQueryBuilder = clone $queryBuilder;

        $sort = $petCollection->getSort();

        if (isset($sort['name'])) {
            $itemsQueryBuilder->sort('name', $sort['name']);
        }

        $itemsQueryBuilder->skip($petCollection->getOffset());
        $itemsQueryBuilder->limit($petCollection->getLimit());

        /** @var Iterator<Pet> $iterator */
        $iterator = $itemsQueryBuilder->getQuery()->execute();
        $petCollection->setItems($iterator->toArray());
    }

    public function findById(string $id): ?Pet
    {
        return $this->documentManager->find(Pet::class, $id);
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

        $this->documentManager->persist($pet);
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

        $this->documentManager->remove($pet);
    }

    public function flush(): void
    {
        $this->documentManager->flush();
    }
}
