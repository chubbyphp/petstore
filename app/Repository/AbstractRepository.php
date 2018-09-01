<?php

declare(strict_types=1);

namespace App\Repository;

use App\Collection\CollectionInterface;
use App\Model\ModelInterface;
use Doctrine\ORM\EntityManager;

abstract class AbstractRepository implements RepositoryInterface
{
    /**
     * @var EntityManager
     */
    protected $entityManager;

    /**
     * @param EntityManager $entityManager
     */
    public function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * @param CollectionInterface $collection
     */
    public function resolveCollection(CollectionInterface $collection)
    {
        $qb = $this->entityManager->getRepository($this->getModelClass())->createQueryBuilder('p');
        $qb->setFirstResult($collection->getOffset());
        $qb->setMaxResults($collection->getLimit());

        $collection->setItems($qb->getQuery()->getResult());
    }

    /**
     * @param string $id
     *
     * @return ModelInterface|null
     */
    public function findById(string $id)
    {
        return $this->entityManager->find($this->getModelClass(), $id);
    }

    /**
     * @param ModelInterface $model
     */
    public function persist(ModelInterface $model)
    {
        $this->entityManager->persist($model);
    }

    /**
     * @param ModelInterface $model
     */
    public function remove(ModelInterface $model)
    {
        $this->entityManager->remove($model);
    }

    public function flush()
    {
        $this->entityManager->flush();
    }

    /**
     * @return string
     */
    abstract protected function getModelClass(): string;
}
