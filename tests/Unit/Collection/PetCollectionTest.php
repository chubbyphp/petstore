<?php

declare(strict_types=1);

namespace App\Tests\Unit\Collection;

use App\Collection\CollectionInterface;
use App\Collection\PetCollection;

/**
 * @covers \App\Collection\PetCollection
 */
final class PetCollectionTest extends CollectionTest
{
    /**
     * @return CollectionInterface
     */
    protected function getCollection(): CollectionInterface
    {
        return new PetCollection();
    }
}
