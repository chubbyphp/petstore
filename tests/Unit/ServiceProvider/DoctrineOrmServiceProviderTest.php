<?php

declare(strict_types=1);

namespace App\Tests\Unit\ServiceProvider;

use App\Mapping\Orm\PetMapping;
use App\Model\Pet;
use App\ServiceProvider\DoctrineOrmServiceProvider;
use PHPUnit\Framework\TestCase;
use Pimple\Container;

/**
 * @covers \App\ServiceProvider\DoctrineOrmServiceProvider
 *
 * @internal
 */
final class DoctrineOrmServiceProviderTest extends TestCase
{
    public function testRegister(): void
    {
        $container = new Container();

        $serviceProvider = new DoctrineOrmServiceProvider();
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
