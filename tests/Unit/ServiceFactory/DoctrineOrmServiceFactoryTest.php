<?php

declare(strict_types=1);

namespace App\Tests\Unit\ServiceFactory;

use App\Mapping\Orm\PetMapping;
use App\Mapping\Orm\VaccinationMapping;
use App\Model\Pet;
use App\Model\Vaccination;
use App\ServiceFactory\DoctrineOrmServiceFactory;
use Chubbyphp\Mock\MockByCallsTrait;
use PHPUnit\Framework\TestCase;

/**
 * @covers \App\ServiceFactory\DoctrineOrmServiceFactory
 *
 * @internal
 */
final class DoctrineOrmServiceFactoryTest extends TestCase
{
    use MockByCallsTrait;

    public function testFactories(): void
    {
        $factories = (new DoctrineOrmServiceFactory())();

        self::assertCount(1, $factories);
    }

    public function testResponseFactory(): void
    {
        $factories = (new DoctrineOrmServiceFactory())();

        self::assertArrayHasKey('doctrine.orm.em.options', $factories);

        $options = $factories['doctrine.orm.em.options']();

        self::assertSame([
            'mappings' => [
                [
                    'type' => 'class_map',
                    'namespace' => 'App\Model',
                    'map' => [
                        Pet::class => PetMapping::class,
                        Vaccination::class => VaccinationMapping::class,
                    ],
                ],
            ],
        ], $options);
    }
}
