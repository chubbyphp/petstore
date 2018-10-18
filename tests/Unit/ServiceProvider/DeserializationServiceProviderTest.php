<?php

declare(strict_types=1);

namespace App\Tests\Unit\ServiceProvider;

use App\Mapping\Deserialization\PetMapping;
use App\Mapping\MappingConfig;
use App\Model\Pet;
use App\ServiceProvider\DeserializationServiceProvider;
use Chubbyphp\Deserialization\Mapping\CallableDenormalizationObjectMapping;
use PHPUnit\Framework\TestCase;
use Pimple\Container;

/**
 * @covers \App\ServiceProvider\DeserializationServiceProvider
 */
final class DeserializationServiceProviderTest extends TestCase
{
    public function testRegister()
    {
        $container = new Container([
            'someService' => function () {
                return new \stdClass();
            },
        ]);

        $serviceProvider = new DeserializationServiceProvider();
        $serviceProvider->register($container);

        self::assertArrayHasKey('deserializer.denormalizer.objectmappings', $container);

        $mappingConfigs = $container['deserializer.mappingConfigs'];

        // hack to tests dependencies
        $mappingConfigs[Pet::class] = new MappingConfig(PetMapping::class, ['someService']);

        $container['deserializer.mappingConfigs'] = $mappingConfigs;

        $mappings = $container['deserializer.denormalizer.objectmappings'];

        self::assertCount(2, $mappings);

        self::assertInstanceOf(CallableDenormalizationObjectMapping::class, $mappings[0]);
        self::assertInstanceOf(CallableDenormalizationObjectMapping::class, $mappings[1]);

        self::assertCount(3, $mappings[0]->getDenormalizationFieldMappings('path'));
        self::assertCount(2, $mappings[1]->getDenormalizationFieldMappings('path'));
    }
}
