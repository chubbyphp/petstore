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
        $petCollectionFilters = new PetCollectionFilters('jerry');

        $petCollectionSort = new PetCollectionSort('asc');

        $vaccinationResponse = new VaccinationResponse('rabid', 'vaccination');

        $petResponse = new PetResponse(
            '019c201f-6a83-7696-9899-50fbf7b2278d',
            '2024-02-10T18:15:00+00:00',
            '2024-02-10T18:15:00+00:00',
            'jerry',
            'tag',
            [$vaccinationResponse],
            'pet',
            [],
        );

        $petCollectionResponse = new PetCollectionResponse(
            5,
            10,
            $petCollectionFilters,
            $petCollectionSort,
            [$petResponse],
            1,
            'petCollection',
            []
        );

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
