<?php

declare(strict_types=1);

namespace App\Tests\Unit\Model;

use App\Model\ModelInterface;
use App\Model\Pet;
use App\Model\Vaccination;
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

        self::assertMatchesRegularExpression('/[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}/', $pet->getId());
        self::assertInstanceOf(\DateTimeImmutable::class, $pet->getCreatedAt());
        self::assertNull($pet->getUpdatedAt());
        self::assertNull($pet->getTag());
        self::assertCount(0, $pet->getVaccinations());

        $now = new \DateTimeImmutable();

        $vaccination1 = new Vaccination();
        $vaccination1->setName('Rabies');

        $vaccination2 = new Vaccination();
        $vaccination2->setName('Feline Acquired Immune Deficiency Syndrome');

        $vaccination3 = new Vaccination();
        $vaccination3->setName('Panleukopenia');

        $pet->setUpdatedAt($now);
        $pet->setName('Lucas');
        $pet->setTag('2018 OHIO DOG 87123 LUCAS');
        $pet->setVaccinations([$vaccination2, $vaccination3]);

        self::assertNull($vaccination1->getPet());
        self::assertSame($pet, $vaccination2->getPet());
        self::assertSame($pet, $vaccination3->getPet());

        $pet->setVaccinations([$vaccination1, $vaccination2]);

        self::assertSame($pet, $vaccination1->getPet());
        self::assertSame($pet, $vaccination2->getPet());
        self::assertNull($vaccination3->getPet());

        self::assertSame($now, $pet->getUpdatedAt());
        self::assertSame('Lucas', $pet->getName());
        self::assertSame('2018 OHIO DOG 87123 LUCAS', $pet->getTag());

        $vaccinations = $pet->getVaccinations();

        self::assertCount(2, $vaccinations);

        self::assertSame($vaccination1, array_shift($vaccinations));
        self::assertSame($vaccination2, array_shift($vaccinations));

        self::assertSame('Rabies', $vaccination1->getName());
        self::assertSame($pet, $vaccination1->getPet());

        self::assertSame('Feline Acquired Immune Deficiency Syndrome', $vaccination2->getName());
        self::assertSame($pet, $vaccination2->getPet());

        self::assertSame([
            'id' => $pet->getId(),
            'createdAt' => $pet->getCreatedAt(),
            'updatedAt' => $pet->getUpdatedAt(),
            'name' => $pet->getName(),
            'tag' => $pet->getTag(),
            'vaccinations' => [
                [
                    'name' => $pet->getVaccinations()[0]->getName(),
                ],
                [
                    'name' => $pet->getVaccinations()[1]->getName(),
                ],
            ],
        ], $pet->jsonSerialize());
    }
}
