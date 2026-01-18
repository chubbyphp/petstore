<?php

declare(strict_types=1);

namespace App\Tests\Unit\Pet\Collection;

use App\Core\Collection\CollectionInterface;
use App\Pet\Collection\PetCollection;
use App\Tests\Unit\Core\Collection\CollectionTest;

/**
 * @covers \App\Pet\Collection\PetCollection
 *
 * @internal
 */
final class PetCollectionTest extends CollectionTest
{
    protected function getCollection(): CollectionInterface
    {
        return new PetCollection();
    }
}
