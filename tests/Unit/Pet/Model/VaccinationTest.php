<?php

declare(strict_types=1);

namespace App\Tests\Unit\Pet\Model;

use App\Pet\Model\Pet;
use App\Pet\Model\Vaccination;
use App\Tests\Helper\PatternHelper;
use PHPUnit\Framework\TestCase;

/**
 * @covers \App\Pet\Model\Vaccination
 *
 * @internal
 */
final class VaccinationTest extends TestCase
{
    public function testGetSet(): void
    {
        $vaccination = new Vaccination();

        self::assertMatchesRegularExpression(PatternHelper::UUID_PATTERN, $vaccination->getId());

        $pet = new Pet();

        self::assertNull($vaccination->getPet());

        $vaccination->setName('Rabies');
        $vaccination->setPet($pet);

        self::assertSame('Rabies', $vaccination->getName());

        self::assertSame($pet, $vaccination->getPet());

        self::assertSame(['name' => $vaccination->getName()], $vaccination->jsonSerialize());
    }
}
