<?php

declare(strict_types=1);

namespace App\Tests\Unit\Dto\Collection;

use App\Dto\Collection\PetCollectionFilters;
use App\Dto\Collection\PetCollectionResponse;
use App\Dto\Collection\PetCollectionSort;
use App\Dto\Model\PetResponse;
use App\Dto\Model\VaccinationResponse;
use PHPUnit\Framework\TestCase;

/**
 * @covers \App\Dto\Collection\PetCollectionFilters
 * @covers \App\Dto\Collection\PetCollectionResponse
 * @covers \App\Dto\Collection\PetCollectionSort
 *
 * @internal
 */
final class PetCollectionResponseTest extends TestCase
{
    public function testCreateCollection(): void
    {
        $petCollectionFilters = new PetCollectionFilters();
        $petCollectionFilters->name = 'jerry';

        $petCollectionSort = new PetCollectionSort();
        $petCollectionSort->name = 'asc';

        $vaccinationResponse = new VaccinationResponse();
        $vaccinationResponse->name = 'rabid';
        $vaccinationResponse->_type = 'vaccination';

        $petResponse = new PetResponse();
        $petResponse->id = '93c75323-38a2-4c89-b500-7fc5d9f0c602';
        $petResponse->createdAt = '2024-02-10T18:15:00+00:00';
        $petResponse->updatedAt = '2024-02-10T18:15:00+00:00';
        $petResponse->name = 'jerry';
        $petResponse->tag = 'efd3c6a6-a12e-4551-bf89-62727fba0d92';
        $petResponse->vaccinations = [$vaccinationResponse];
        $petResponse->_type = 'pet';
        $petResponse->_links = [];

        $petCollectionResponse = new PetCollectionResponse();
        $petCollectionResponse->offset = 5;
        $petCollectionResponse->limit = 10;
        $petCollectionResponse->filters = $petCollectionFilters;
        $petCollectionResponse->sort = $petCollectionSort;
        $petCollectionResponse->items = [$petResponse];
        $petCollectionResponse->count = 1;
        $petCollectionResponse->_type = 'petCollection';
        $petCollectionResponse->_links = [];

        self::assertSame([
            'offset' => 5,
            'limit' => 10,
            'filters' => [
                'name' => 'jerry',
            ],
            'sort' => [
                'name' => 'asc',
            ],
            'items' => [
                [
                    'id' => '93c75323-38a2-4c89-b500-7fc5d9f0c602',
                    'createdAt' => '2024-02-10T18:15:00+00:00',
                    'updatedAt' => '2024-02-10T18:15:00+00:00',
                    'name' => 'jerry',
                    'tag' => 'efd3c6a6-a12e-4551-bf89-62727fba0d92',
                    'vaccinations' => [
                        [
                            'name' => 'rabid',
                            '_type' => 'vaccination',
                        ],
                    ],
                    '_type' => 'pet',
                    '_links' => [],
                ],
            ],
            'count' => 1,
            '_type' => 'petCollection',
            '_links' => [],
        ], $petCollectionResponse->jsonSerialize());
    }
}
