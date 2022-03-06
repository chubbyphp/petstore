<?php

declare(strict_types=1);

namespace App\Tests\Unit\Collection;

use App\Collection\AbstractCollection;
use App\Collection\CollectionInterface;
use PHPUnit\Framework\TestCase;

/**
 * @covers \App\Collection\AbstractCollection
 *
 * @internal
 */
class CollectionTest extends TestCase
{
    public function testGetSet(): void
    {
        $collection = $this->getCollection();

        static::assertSame(0, $collection->getOffset());
        static::assertSame(20, $collection->getLimit());
        static::assertSame([], $collection->getFilters());
        static::assertSame([], $collection->getSort());
        static::assertSame(0, $collection->getCount());
        static::assertSame([], $collection->getItems());

        $object = new \stdClass();

        $collection->setOffset(5);
        $collection->setLimit(15);
        $collection->setFilters(['name' => 'sample']);
        $collection->setSort(['name' => 'asc']);
        $collection->setCount(6);
        $collection->setItems([$object]);

        static::assertSame(5, $collection->getOffset());
        static::assertSame(15, $collection->getLimit());
        static::assertSame(['name' => 'sample'], $collection->getFilters());
        static::assertSame(['name' => 'asc'], $collection->getSort());
        static::assertSame(6, $collection->getCount());
        static::assertSame([$object], $collection->getItems());
    }

    protected function getCollection(): CollectionInterface
    {
        return new class() extends AbstractCollection {
        };
    }
}
