<?php

declare(strict_types=1);

namespace App\ServiceFactory\Factory\Collection;

use App\Factory\Collection\PetCollectionFactory;

final class PetCollectionFactoryFactory
{
    public function __invoke(): PetCollectionFactory
    {
        return new PetCollectionFactory();
    }
}
