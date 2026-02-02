<?php

declare(strict_types=1);

namespace App\Tests\Unit\Pet\Dto\Collection;

use App\Pet\Dto\Collection\PetCollectionFilters;
use App\Pet\Dto\Collection\PetCollectionResponse;
use App\Pet\Dto\Collection\PetCollectionSort;
use App\Pet\Dto\Model\PetResponse;
use App\Pet\Dto\Model\VaccinationResponse;
use PHPUnit\Framework\TestCase;

/**
 * @covers \App\Pet\Dto\Collection\PetCollectionFilters
 * @covers \App\Pet\Dto\Collection\PetCollectionResponse
 * @covers \App\Pet\Dto\Collection\PetCollectionSort
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
        $petResponse->id = '019c201f-6a83-7696-9899-50fbf7b2278d';
        $petResponse->createdAt = '2024-02-10T18:15:00+00:00';
        $petResponse->updatedAt = '2024-02-10T18:15:00+00:00';
        $petResponse->name = 'jerry';
        $petResponse->tag = 'tag';
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
                    'id' => '019c201f-6a83-7696-9899-50fbf7b2278d',
                    'createdAt' => '2024-02-10T18:15:00+00:00',
                    'updatedAt' => '2024-02-10T18:15:00+00:00',
                    'name' => 'jerry',
                    'tag' => 'tag',
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
