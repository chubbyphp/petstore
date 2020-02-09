<?php

declare(strict_types=1);

namespace App\Tests\Unit\Model;

use App\Model\ModelInterface;
use App\Model\Pet;
use App\Model\Vaccination;
use App\Tests\Helper\AssertHelper;
use PHPUnit\Framework\TestCase;

/**
 * @covers \App\Model\Pet
 *
 * @internal
 */
final class PetTest extends TestCase
{
    public function testGetSet(): void
    {
        $pet = new Pet();

        self::assertInstanceOf(ModelInterface::class, $pet);

        self::assertRegExp('/[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}/', $pet->getId());
        self::assertInstanceOf(\DateTime::class, $pet->getCreatedAt());
        self::assertNull($pet->getUpdatedAt());
        self::assertNull($pet->getTag());
        self::assertCount(0, $pet->getVaccinations());

        $now = new \DateTime();

        $vaccination1 = new Vaccination();
        $vaccination1->setName('Rabies');

        $vaccination2 = new Vaccination();
        $vaccination2->setName('Feline Acquired Immune Deficiency Syndrome');

        $pet->setUpdatedAt($now);
        $pet->setName('Lucas');
        $pet->setTag('2018 OHIO DOG 87123 LUCAS');
        $pet->setVaccinations([$vaccination2]);
        $pet->setVaccinations([$vaccination1, $vaccination2]);

        self::assertSame($now, $pet->getUpdatedAt());
        self::assertSame('Lucas', $pet->getName());
        self::assertSame('2018 OHIO DOG 87123 LUCAS', $pet->getTag());

        $vaccinations = $pet->getVaccinations();

        self::assertCount(2, $vaccinations);

        self::assertSame($vaccination1, array_shift($vaccinations));
        self::assertSame($vaccination2, array_shift($vaccinations));

        self::assertSame('Rabies', AssertHelper::readProperty('name', $vaccination1));
        self::assertSame($pet, AssertHelper::readProperty('pet', $vaccination1));

        self::assertSame(
            'Feline Acquired Immune Deficiency Syndrome',
            AssertHelper::readProperty('name', $vaccination2)
        );
        self::assertSame($pet, AssertHelper::readProperty('pet', $vaccination2));

        $id = $pet->getId();
        $createdAt = $pet->getCreatedAt();

        $pet->reset();

        self::assertSame($id, $pet->getId());
        self::assertSame($createdAt, $pet->getCreatedAt());

        self::assertNull($pet->getUpdatedAt());
        self::assertNull($pet->getTag());
        self::assertCount(0, $pet->getVaccinations());
    }
}
