<?php

declare(strict_types=1);

namespace App\Tests\Unit\Factory\Collection;

use App\Collection\PetCollection;
use App\Factory\Collection\PetCollectionFactory;
use PHPUnit\Framework\TestCase;

/**
 * @covers \App\Factory\Collection\PetCollectionFactory
 *
 * @internal
 */
final class PetCollectionFactoryTest extends TestCase
{
    public function testCreate(): void
    {
        $factory = new PetCollectionFactory();

        self::assertInstanceOf(PetCollection::class, $factory->create());
    }
}
