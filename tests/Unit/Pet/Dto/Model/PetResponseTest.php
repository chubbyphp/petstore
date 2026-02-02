<?php

declare(strict_types=1);

namespace App\Tests\Unit\Pet\Dto\Model;

use App\Pet\Dto\Model\PetResponse;
use App\Pet\Dto\Model\VaccinationResponse;
use PHPUnit\Framework\TestCase;

/**
 * @covers \App\Pet\Dto\Model\PetResponse
 * @covers \App\Pet\Dto\Model\VaccinationResponse
 *
 * @internal
 */
final class PetResponseTest extends TestCase
{
    public function testCreateCollection(): void
    {
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

        self::assertSame([
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
        ], $petResponse->jsonSerialize());
    }
}
