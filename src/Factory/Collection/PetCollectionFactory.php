<?php

declare(strict_types=1);

namespace App\Factory\Collection;

use App\Collection\CollectionInterface;
use App\Collection\PetCollection;
use App\Factory\CollectionFactoryInterface;

final class PetCollectionFactory implements CollectionFactoryInterface
{
    /**
     * @return PetCollection|CollectionInterface
     */
    public function create(): CollectionInterface
    {
        return new PetCollection();
    }
}
