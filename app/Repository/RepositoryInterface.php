<?php

declare(strict_types=1);

namespace App\Repository;

use App\Collection\CollectionInterface;
use App\Model\ModelInterface;

interface RepositoryInterface
{
    public function resolveCollection(CollectionInterface $collection): void;

    public function findById(string $id): ?ModelInterface;

    public function persist(ModelInterface $model): void;

    public function remove(ModelInterface $model): void;

    public function flush(): void;
}
