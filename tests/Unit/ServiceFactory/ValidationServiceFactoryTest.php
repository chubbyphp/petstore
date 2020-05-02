<?php

declare(strict_types=1);

namespace App\Tests\Unit\ServiceFactory;

use App\Collection\PetCollection;
use App\Mapping\Validation\PetCollectionMapping;
use App\Mapping\Validation\PetMapping;
use App\Mapping\Validation\VaccinationMapping;
use App\Model\Pet;
use App\Model\Vaccination;
use App\ServiceFactory\ValidationServiceFactory;
use Chubbyphp\Container\Container;
use Chubbyphp\Validation\Mapping\LazyValidationMappingProvider;
use PHPUnit\Framework\TestCase;

/**
 * @covers \App\ServiceFactory\ValidationServiceFactory
 *
 * @internal
 */
final class ValidationServiceFactoryTest extends TestCase
{
    public function testFactories(): void
    {
        $factories = (new ValidationServiceFactory())();

        self::assertCount(4, $factories);
    }

    public function testObjectMappings(): void
    {
        $expectedMappings = [
            PetCollectionMapping::class => PetCollection::class,
            PetMapping::class => Pet::class,
            VaccinationMapping::class => Vaccination::class,
        ];

        $factories = (new ValidationServiceFactory())();

        $container = new Container();
        $container->factory('validator.mappings', $factories['validator.mappings']);

        foreach ($expectedMappings as $mappingClass => $class) {
            $container->factory($mappingClass, $factories[$mappingClass]);
        }

        self::assertArrayHasKey('validator.mappings', $factories);

        $mappings = $container->get('validator.mappings');

        self::assertIsArray($mappings);

        self::assertCount(count($expectedMappings), $mappings);

        foreach ($expectedMappings as $mappingClass => $class) {
            /** @var LazyValidationMappingProvider $mapping */
            $mapping = array_shift($mappings);

            self::assertInstanceOf(LazyValidationMappingProvider::class, $mapping);

            self::assertSame($class, $mapping->getClass());

            $mapping->getValidationClassMapping('path');
        }
    }
}
