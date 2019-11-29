<?php

declare(strict_types=1);

namespace App\Tests\Unit\ServiceFactory;

use App\Factory\Collection\PetCollectionFactory;
use App\Factory\Model\PetFactory;
use App\ServiceFactory\FactoryServiceFactory;
use PHPUnit\Framework\TestCase;

/**
 * @covers \App\ServiceFactory\FactoryServiceFactory
 *
 * @internal
 */
final class FactoryServiceFactoryTest extends TestCase
{
    public function testFactories(): void
    {
        $factories = (new FactoryServiceFactory())();

        self::assertCount(2, $factories);
    }

    public function testPetCollectionFactory(): void
    {
        $factories = (new FactoryServiceFactory())();

        self::assertArrayHasKey(PetCollectionFactory::class, $factories);

        self::assertInstanceOf(PetCollectionFactory::class, $factories[PetCollectionFactory::class]());
    }

    public function testPetFactory(): void
    {
        $factories = (new FactoryServiceFactory())();

        self::assertArrayHasKey(PetFactory::class, $factories);

        self::assertInstanceOf(PetFactory::class, $factories[PetFactory::class]());
    }
}
