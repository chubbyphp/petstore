<?php

declare(strict_types=1);

namespace App\Tests\Unit\ServiceProvider;

use App\Mapping\Orm\PetMapping;
use App\Model\Pet;
use App\ServiceProvider\DoctrineServiceProvider;
use PHPUnit\Framework\TestCase;
use Pimple\Container;

/**
 * @covers \App\ServiceProvider\DoctrineServiceProvider
 *
 * @internal
 */
final class DoctrineServiceProviderTest extends TestCase
{
    public function testRegister(): void
    {
        $container = new Container();

        $serviceProvider = new DoctrineServiceProvider();
        $serviceProvider->register($container);

        self::assertArrayHasKey('doctrine.orm.em.options', $container);

        self::assertEquals([
            'mappings' => [
                [
                    'type' => 'class_map',
                    'namespace' => 'App\Model',
                    'map' => [
                        Pet::class => PetMapping::class,
                    ],
                ],
            ],
        ], $container['doctrine.orm.em.options']);
    }
}
