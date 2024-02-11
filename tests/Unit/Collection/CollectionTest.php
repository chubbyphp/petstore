<?php

declare(strict_types=1);

namespace App\Tests\Unit\Collection;

use App\Collection\AbstractCollection;
use App\Collection\CollectionInterface;
use App\Model\ModelInterface;
use Chubbyphp\Mock\Call;
use Chubbyphp\Mock\MockByCallsTrait;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * @covers \App\Collection\AbstractCollection
 *
 * @internal
 */
class CollectionTest extends TestCase
{
    use MockByCallsTrait;

    public function testGetSet(): void
    {
        $collection = $this->getCollection();

        self::assertSame(0, $collection->getOffset());
        self::assertSame(20, $collection->getLimit());
        self::assertSame([], $collection->getFilters());
        self::assertSame([], $collection->getSort());
        self::assertSame(0, $collection->getCount());
        self::assertSame([], $collection->getItems());

        /** @var MockObject|ModelInterface $model */
        $model = $this->getMockByCalls(ModelInterface::class, [
            Call::create('jsonSerialize')->with()->willReturn(['id' => '111d1691-8486-4447-997c-d10ce35d1fea']),
        ]);

        $collection->setOffset(5);
        $collection->setLimit(15);
        $collection->setFilters(['name' => 'sample']);
        $collection->setSort(['name' => 'asc']);
        $collection->setCount(6);
        $collection->setItems([$model]);

        self::assertSame(5, $collection->getOffset());
        self::assertSame(15, $collection->getLimit());
        self::assertSame(['name' => 'sample'], $collection->getFilters());
        self::assertSame(['name' => 'asc'], $collection->getSort());
        self::assertSame(6, $collection->getCount());
        self::assertSame([$model], $collection->getItems());
        self::assertSame([
            'offset' => 5,
            'limit' => 15,
            'filters' => [
                'name' => 'sample',
            ],
            'sort' => [
                'name' => 'asc',
            ],
            'items' => [
                0 => [
                    'id' => '111d1691-8486-4447-997c-d10ce35d1fea',
                ],
            ],
            'count' => 6,
        ], $collection->jsonSerialize());
    }

    protected function getCollection(): CollectionInterface
    {
        return new class() extends AbstractCollection {};
    }
}
