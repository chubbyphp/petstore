<?php

declare(strict_types=1);

namespace App\Tests\Unit\Model;

use App\Model\Pet;
use App\Model\Vaccination;
use App\Tests\AssertTrait;
use App\Tests\Helper\AssertHelper;
use PHPUnit\Framework\TestCase;

/**
 * @covers \App\Model\Vaccination
 *
 * @internal
 */
final class VaccinationTest extends TestCase
{
    use AssertTrait;

    public function testGetSet(): void
    {
        $vaccination = new Vaccination();

        self::assertMatchesRegularExpression('/[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}/', $vaccination->getId());

        $pet = new Pet();

        self::assertNull(AssertHelper::readProperty('pet', $vaccination));

        $vaccination->setName('Rabies');
        $vaccination->setPet($pet);

        self::assertSame('Rabies', $vaccination->getName());
        self::assertSame($pet, AssertHelper::readProperty('pet', $vaccination));
    }
}
