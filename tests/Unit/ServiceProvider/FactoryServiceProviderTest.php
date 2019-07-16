<?php

declare(strict_types=1);

namespace App\Tests\Unit\ServiceProvider;

use App\Factory\Collection\PetCollectionFactory;
use App\Factory\Model\PetFactory;
use App\ServiceProvider\FactoryServiceProvider;
use PHPUnit\Framework\TestCase;
use Pimple\Container;

/**
 * @covers \App\ServiceProvider\FactoryServiceProvider
 *
 * @internal
 */
final class FactoryServiceProviderTest extends TestCase
{
    public function testRegister(): void
    {
        $container = new Container();

        $serviceProvider = new FactoryServiceProvider();
        $serviceProvider->register($container);

        self::assertArrayHasKey(PetCollectionFactory::class, $container);
        self::assertArrayHasKey(PetFactory::class, $container);

        self::assertInstanceOf(PetCollectionFactory::class, $container[PetCollectionFactory::class]);
        self::assertInstanceOf(PetFactory::class, $container[PetFactory::class]);
    }
}
