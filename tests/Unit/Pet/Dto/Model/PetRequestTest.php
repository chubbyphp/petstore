<?php

declare(strict_types=1);

namespace App\Tests\Unit\Pet\Dto\Model;

use App\Pet\Dto\Model\PetRequest;
use App\Pet\Dto\Model\VaccinationRequest;
use App\Pet\Model\Pet;
use App\Tests\Helper\PatternHelper;
use PHPUnit\Framework\TestCase;

/**
 * @covers \App\Pet\Dto\Model\PetRequest
 * @covers \App\Pet\Dto\Model\VaccinationRequest
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

        self::assertMatchesRegularExpression(PatternHelper::UUID_PATTERN, $petData['id']);
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

        self::assertMatchesRegularExpression(PatternHelper::UUID_PATTERN, $petData['id']);
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
