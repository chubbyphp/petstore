<?php

declare(strict_types=1);

namespace App\Dto\Collection;

use App\Collection\CollectionInterface;
use App\Collection\PetCollection;

final class PetCollectionRequest implements CollectionRequestInterface
{
    public int $offset;

    public int $limit;

    public PetCollectionFilters $filters;

    public PetCollectionSort $sort;

    public function createCollection(): CollectionInterface
    {
        $collection = new PetCollection();
        $collection->setOffset($this->offset);
        $collection->setLimit($this->limit);
        $collection->setFilters((array) $this->filters);
        $collection->setSort((array) $this->sort);

        return $collection;
    }
}
