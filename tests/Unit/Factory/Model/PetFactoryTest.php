<?php

declare(strict_types=1);

namespace App\Tests\Unit\Factory\Model;

use App\Factory\Model\PetFactory;
use App\Model\Pet;
use PHPUnit\Framework\TestCase;

/**
 * @covers \App\Factory\Model\PetFactory
 */
final class PetFactoryTest extends TestCase
{
    public function testCreate()
    {
        $factory = new PetFactory();

        self::assertInstanceOf(Pet::class, $factory->create());
    }

    public function testGetClass()
    {
        $factory = new PetFactory();

        self::assertSame(Pet::class, $factory->getClass());
    }
}
