<?php

declare(strict_types=1);

namespace App\Tests\Unit\Core\Dto\Collection;

use App\Core\Dto\Collection\AbstractCollectionResponse;
use App\Core\Dto\Collection\CollectionFiltersInterface;
use App\Core\Dto\Collection\CollectionSortInterface;
use App\Core\Dto\Model\ModelResponseInterface;
use Chubbyphp\Mock\MockMethod\WithReturn;
use Chubbyphp\Mock\MockObjectBuilder;
use PHPUnit\Framework\TestCase;

/**
 * @covers \App\Core\Dto\Collection\AbstractCollectionResponse
 *
 * @internal
 */
final class CollectionResponseTest extends TestCase
{
    public function testJsonSerialize(): void
    {
        $builder = new MockObjectBuilder();

        /** @var ModelResponseInterface $modelResponse */
        $modelResponse = $builder->create(ModelResponseInterface::class, [
            new WithReturn(
                'jsonSerialize',
                [],
                ['id' => '111d1691-8486-4447-997c-d10ce35d1fea']
            ),
        ]);

        $collectionResponse = $this->getCollectionResponse();
        $collectionResponse->offset = 0;
        $collectionResponse->limit = 20;
        $collectionResponse->items = [$modelResponse];
        $collectionResponse->count = 1;
        $collectionResponse->_links = [];
        $collectionResponse->_type = 'unknown';

        self::assertSame([
            'offset' => 0,
            'limit' => 20,
            'filters' => [
                'name' => 'John',
            ],
            'sort' => [
                'name' => 'asc',
            ],
            'items' => [
                [
                    'id' => '111d1691-8486-4447-997c-d10ce35d1fea',
                ],
            ],
            'count' => 1,
            '_type' => 'unknown',
            '_links' => [],
        ], $collectionResponse->jsonSerialize());
    }

    protected function getCollectionResponse(): AbstractCollectionResponse
    {
        return new class extends AbstractCollectionResponse {
            protected function getFilters(): CollectionFiltersInterface
            {
                return new class implements CollectionFiltersInterface {
                    public function jsonSerialize(): array
                    {
                        return ['name' => 'John'];
                    }
                };
            }

            protected function getSort(): CollectionSortInterface
            {
                return new class implements CollectionSortInterface {
                    public function jsonSerialize(): array
                    {
                        return ['name' => 'asc'];
                    }
                };
            }
        };
    }
}
