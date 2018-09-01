<?php

declare(strict_types=1);

namespace App\Factory\Collection;

use App\Collection\CollectionInterface;
use App\Collection\PetCollection;

final class PetCollectionFactory implements FactoryInterface
{
    /**
     * @return CollectionInterface
     */
    public function create(): CollectionInterface
    {
        return new PetCollection();
    }
}
