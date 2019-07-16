<?php

declare(strict_types=1);

namespace App\Tests\Unit\ServiceProvider;

use App\Mapping\MappingConfig;
use App\ServiceProvider\DeserializationServiceProvider;
use Chubbyphp\Deserialization\Mapping\CallableDenormalizationObjectMapping;
use Chubbyphp\Deserialization\Mapping\DenormalizationObjectMappingInterface;
use PHPUnit\Framework\TestCase;
use Pimple\Container;

/**
 * @covers \App\ServiceProvider\DeserializationServiceProvider
 *
 * @internal
 */
final class DeserializationServiceProviderTest extends TestCase
{
    public function testRegister(): void
    {
        $container = new Container([
            'someService' => function () {
                return new \stdClass();
            },
        ]);

        $serviceProvider = new DeserializationServiceProvider();
        $serviceProvider->register($container);

        self::assertArrayHasKey('deserializer.denormalizer.objectmappings', $container);

        $mappings = $container['deserializer.denormalizer.objectmappings'];

        self::assertCount(2, $mappings);

        self::assertInstanceOf(CallableDenormalizationObjectMapping::class, $mappings[0]);
        self::assertInstanceOf(CallableDenormalizationObjectMapping::class, $mappings[1]);

        self::assertCount(3, $mappings[0]->getDenormalizationFieldMappings('path'));
        self::assertCount(2, $mappings[1]->getDenormalizationFieldMappings('path'));
    }

    public function testMappings(): void
    {
        $container = new Container([
            'sampleService' => function () {
                return new \stdClass();
            },
        ]);

        $serviceProvider = new DeserializationServiceProvider();
        $serviceProvider->register($container);

        $stdClassMapping = new class(new \stdClass()) implements DenormalizationObjectMappingInterface {
            public function __construct(\stdClass $sampleService)
            {
            }

            /**
             * @return string
             */
            public function getClass(): string
            {
                return \stdClass::class;
            }

            /**
             * @param string      $path
             * @param string|null $type
             *
             * @throws DeserializerRuntimeException
             *
             * @return callable
             */
            public function getDenormalizationFactory(string $path, string $type = null): callable
            {
                return function () {
                    return new \stdClass();
                };
            }

            /**
             * @param string      $path
             * @param string|null $type
             *
             * @throws DeserializerRuntimeException
             *
             * @return DenormalizationFieldMappingInterface[]
             */
            public function getDenormalizationFieldMappings(string $path, string $type = null): array
            {
                return [];
            }
        };

        $stdClassMappingClass = get_class($stdClassMapping);

        $mappingConfigs = [];
        $mappingConfigs[\stdClass::class] = new MappingConfig($stdClassMappingClass, ['sampleService']);

        $container['deserializer.mappingConfigs'] = $mappingConfigs;

        /** @var CallableDenormalizationObjectMapping[] $mappings */
        $mappings = $container['deserializer.denormalizer.objectmappings'];

        self::assertCount(1, $mappings);

        self::assertInstanceOf(CallableDenormalizationObjectMapping::class, $mappings[0]);

        self::assertCount(0, $mappings[0]->getDenormalizationFieldMappings('path'));
    }
}
