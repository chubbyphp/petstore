<?php

declare(strict_types=1);

namespace App\Tests\Unit\Pet\Dto\Collection;

use App\Pet\Collection\PetCollection;
use App\Pet\Dto\Collection\PetCollectionFilters;
use App\Pet\Dto\Collection\PetCollectionRequest;
use App\Pet\Dto\Collection\PetCollectionSort;
use PHPUnit\Framework\TestCase;

/**
 * @covers \App\Pet\Dto\Collection\PetCollectionFilters
 * @covers \App\Pet\Dto\Collection\PetCollectionRequest
 * @covers \App\Pet\Dto\Collection\PetCollectionSort
 *
 * @internal
 */
final class PetCollectionRequestTest extends TestCase
{
    public function testCreateCollection(): void
    {
        $petCollectionFilters = new PetCollectionFilters('jerry');

        $petCollectionSort = new PetCollectionSort('asc');

        $petCollectionRequest = new PetCollectionRequest(
            5,
            10,
            $petCollectionFilters,
            $petCollectionSort
        );

        $petCollection = $petCollectionRequest->createCollection();

        self::assertInstanceOf(PetCollection::class, $petCollection);

        self::assertSame([
            'offset' => 5,
            'limit' => 10,
            'filters' => [
                'name' => 'jerry',
            ],
            'sort' => [
                'name' => 'asc',
            ],
            'items' => [],
            'count' => 0,
        ], $petCollection->jsonSerialize());
    }
}
