<?php

declare(strict_types=1);

namespace App\Tests\Unit\Model;

use App\Model\Vaccination;
use App\Tests\Helper\AssertTrait;
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
        $vaccination->setName('Rabies');

        self::assertSame('Rabies', $vaccination->getName());
    }
}
