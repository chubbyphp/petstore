<?php

declare(strict_types=1);

namespace App\Tests\Unit\Pet\Collection;

use App\Pet\Collection\PetCollection;
use Chubbyphp\Api\Collection\CollectionInterface;
use PHPUnit\Framework\TestCase;

/**
 * @covers \App\Pet\Collection\PetCollection
 *
 * @internal
 */
final class PetCollectionTest extends TestCase
{
    public function testGetSet(): void
    {
        $collection = new PetCollection();

        self::assertInstanceOf(CollectionInterface::class, $collection);
    }
}
