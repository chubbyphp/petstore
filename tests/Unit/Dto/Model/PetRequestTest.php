<?php

declare(strict_types=1);

namespace App\Tests\Unit\Dto\Model;

use App\Dto\Model\PetRequest;
use App\Dto\Model\VaccinationRequest;
use App\Model\Pet;
use PHPUnit\Framework\TestCase;

/**
 * @covers \App\Dto\Model\PetRequest
 * @covers \App\Dto\Model\VaccinationRequest
 *
 * @internal
 */
final class PetRequestTest extends TestCase
{
    public function testCreateModel(): void
    {
        $vaccinationRequest = new VaccinationRequest();
        $vaccinationRequest->name = 'rabid';

        $petRequest = new PetRequest();
        $petRequest->name = 'jerry';
        $petRequest->tag = 'efd3c6a6-a12e-4551-bf89-62727fba0d92';
        $petRequest->vaccinations = [$vaccinationRequest];

        /** @var Pet $pet */
        $pet = $petRequest->createModel();

        self::assertInstanceOf(Pet::class, $pet);

        $petData = $pet->jsonSerialize();

        self::assertMatchesRegularExpression('/^[0-9a-f]{8}-[0-9a-f]{4}-4[0-9a-f]{3}-(8|9|a|b)[0-9a-f]{3}-[0-9a-f]{12}$/i', $petData['id']);
        self::assertMatchesRegularExpression('/^\d{4}-\d{2}-\d{2}T\d{2}:\d{2}:\d{2}\+\d{2}:\d{2}$/', $petData['createdAt']->format('c'));
        self::assertNull($petData['updatedAt']);
        self::assertSame('jerry', $petData['name']);
        self::assertSame('efd3c6a6-a12e-4551-bf89-62727fba0d92', $petData['tag']);
        self::assertSame([
            [
                'name' => 'rabid',
            ],
        ], $petData['vaccinations']);
    }

    public function testUpdateModel(): void
    {
        $vaccinationRequest = new VaccinationRequest();
        $vaccinationRequest->name = 'rabid';

        $petRequest = new PetRequest();
        $petRequest->name = 'jerry';
        $petRequest->tag = 'efd3c6a6-a12e-4551-bf89-62727fba0d92';
        $petRequest->vaccinations = [$vaccinationRequest];

        $pet = new Pet();

        /** @var Pet $pet */
        $pet = $petRequest->updateModel($pet);

        self::assertInstanceOf(Pet::class, $pet);

        $petData = $pet->jsonSerialize();

        self::assertMatchesRegularExpression('/^[0-9a-f]{8}-[0-9a-f]{4}-4[0-9a-f]{3}-(8|9|a|b)[0-9a-f]{3}-[0-9a-f]{12}$/i', $petData['id']);
        self::assertMatchesRegularExpression('/^\d{4}-\d{2}-\d{2}T\d{2}:\d{2}:\d{2}\+\d{2}:\d{2}$/', $petData['createdAt']->format('c'));
        self::assertMatchesRegularExpression('/^\d{4}-\d{2}-\d{2}T\d{2}:\d{2}:\d{2}\+\d{2}:\d{2}$/', $petData['updatedAt']->format('c'));
        self::assertSame('jerry', $petData['name']);
        self::assertSame('efd3c6a6-a12e-4551-bf89-62727fba0d92', $petData['tag']);
        self::assertSame([
            [
                'name' => 'rabid',
            ],
        ], $petData['vaccinations']);
    }
}
