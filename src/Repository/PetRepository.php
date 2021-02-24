<?php

declare(strict_types=1);

namespace App\Repository;

use App\Collection\CollectionInterface;
use App\Collection\PetCollection;
use App\Model\ModelInterface;
use App\Model\Pet;
use Doctrine\ODM\MongoDB\DocumentManager;
use Doctrine\ODM\MongoDB\Repository\DocumentRepository;

final class PetRepository implements RepositoryInterface
{
    private DocumentManager $documentManager;

    public function __construct(DocumentManager $documentManager)
    {
        $this->documentManager = $documentManager;
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

        /** @var DocumentRepository $documentRepository */
        $documentRepository = $this->documentManager->getRepository(Pet::class);

        $queryBuilder = $documentRepository->createQueryBuilder();

        $filters = $petCollection->getFilters();

        if (isset($filters['name'])) {
            $queryBuilder->field('name')->text($filters['name']);
        }

        $countQueryBuilder = clone $queryBuilder;
        $countQueryBuilder->count();

        $petCollection->setCount($countQueryBuilder->getQuery()->execute());

        $itemsQueryBuilder = clone $queryBuilder;

        foreach ($petCollection->getSort() as $field => $order) {
            $itemsQueryBuilder->sort($field, $order);
        }

        $itemsQueryBuilder->skip($petCollection->getOffset());
        $itemsQueryBuilder->limit($petCollection->getLimit());

        $petCollection->setItems($itemsQueryBuilder->getQuery()->execute()->toArray());
    }

    /**
     * @return Pet|ModelInterface|null
     */
    public function findById(string $id): ?ModelInterface
    {
        return $this->documentManager->find(Pet::class, $id);
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

        $this->documentManager->persist($pet);
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

        $this->documentManager->remove($pet);
    }

    public function flush(): void
    {
        $this->documentManager->flush();
    }
}
