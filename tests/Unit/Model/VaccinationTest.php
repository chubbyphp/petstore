<?php

declare(strict_types=1);

namespace App\Tests\Unit\Model;

use App\Model\Vaccination;
use PHPUnit\Framework\TestCase;

/**
 * @covers \App\Model\Vaccination
 *
 * @internal
 */
final class VaccinationTest extends TestCase
{
    public function testGetSet(): void
    {
        $vaccination = new Vaccination();

        self::assertRegExp('/[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}/', $vaccination->getId());

        $vaccination->setName('Rabies');

        self::assertSame('Rabies', $vaccination->getName());

        $id = $vaccination->getId();

        $vaccination->reset();

        self::assertSame($id, $vaccination->getId());

        $reflectionProperty = new \ReflectionProperty($vaccination, 'name');
        $reflectionProperty->setAccessible(true);

        self::assertNull($reflectionProperty->getValue($vaccination));
    }
}
