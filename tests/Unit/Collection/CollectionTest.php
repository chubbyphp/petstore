<?php

declare(strict_types=1);

namespace App\Tests\Unit\Collection;

use App\Collection\AbstractCollection;
use App\Collection\CollectionInterface;
use PHPUnit\Framework\TestCase;

/**
 * @covers \App\Collection\AbstractCollection
 */
class CollectionTest extends TestCase
{
    public function testGetSet()
    {
        $collection = $this->getCollection();

        self::assertSame(0, $collection->getOffset());
        self::assertSame(20, $collection->getLimit());
        self::assertSame(0, $collection->getCount());
        self::assertSame([], $collection->getItems());

        $object = new \stdClass();

        $collection->setOffset(5);
        $collection->setLimit(15);
        $collection->setCount(6);
        $collection->setItems([$object]);

        self::assertSame(5, $collection->getOffset());
        self::assertSame(15, $collection->getLimit());
        self::assertSame(6, $collection->getCount());
        self::assertSame([$object], $collection->getItems());
    }

    /**
     * @return CollectionInterface
     */
    protected function getCollection(): CollectionInterface
    {
        return new class() extends AbstractCollection {
        };
    }
}
