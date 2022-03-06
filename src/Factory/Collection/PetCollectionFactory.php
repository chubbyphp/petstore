<?php

declare(strict_types=1);

namespace App\Factory\Collection;

use App\Collection\PetCollection;
use App\Factory\CollectionFactoryInterface;

final class PetCollectionFactory implements CollectionFactoryInterface
{
    public function create(): PetCollection
    {
        return new PetCollection();
    }
}
