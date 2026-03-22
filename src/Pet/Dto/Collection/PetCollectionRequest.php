<?php

declare(strict_types=1);

namespace App\Pet\Dto\Collection;

use App\Pet\Collection\PetCollection;
use Chubbyphp\Api\Collection\CollectionInterface;
use Chubbyphp\Api\Dto\Collection\CollectionRequestInterface;

final readonly class PetCollectionRequest implements CollectionRequestInterface
{
    public function __construct(
        public int $offset,
        public int $limit,
        public PetCollectionFilters $filters,
        public PetCollectionSort $sort
    ) {}

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
