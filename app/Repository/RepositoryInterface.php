<?php

declare(strict_types=1);

namespace App\Repository;

use App\Collection\CollectionInterface;
use App\Model\ModelInterface;

interface RepositoryInterface
{
    /**
     * @param CollectionInterface $collection
     */
    public function resolveCollection(CollectionInterface $collection): void;

    /**
     * @param string $id
     *
     * @return ModelInterface|null
     */
    public function findById(string $id): ?ModelInterface;

    /**
     * @param ModelInterface $model
     */
    public function persist(ModelInterface $model): void;

    /**
     * @param ModelInterface $model
     */
    public function remove(ModelInterface $model): void;

    public function flush(): void;
}
