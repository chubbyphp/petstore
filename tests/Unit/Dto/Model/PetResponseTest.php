<?php

declare(strict_types=1);

namespace App\Tests\Unit\Dto\Model;

use App\Dto\Model\PetResponse;
use App\Dto\Model\VaccinationResponse;
use PHPUnit\Framework\TestCase;

/**
 * @covers \App\Dto\Model\PetResponse
 * @covers \App\Dto\Model\VaccinationResponse
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
        $petResponse->id = '93c75323-38a2-4c89-b500-7fc5d9f0c602';
        $petResponse->createdAt = '2024-02-10T18:15:00+00:00';
        $petResponse->updatedAt = '2024-02-10T18:15:00+00:00';
        $petResponse->name = 'jerry';
        $petResponse->tag = 'efd3c6a6-a12e-4551-bf89-62727fba0d92';
        $petResponse->vaccinations = [$vaccinationResponse];
        $petResponse->_type = 'pet';
        $petResponse->_links = [];

        self::assertSame([
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
        ], $petResponse->jsonSerialize());
    }
}
