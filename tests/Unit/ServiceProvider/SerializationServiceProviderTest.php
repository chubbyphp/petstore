<?php

declare(strict_types=1);

namespace App\Tests\Unit\ServiceProvider;

use App\Mapping\MappingConfig;
use App\ServiceProvider\SerializationServiceProvider;
use Chubbyphp\Mock\MockByCallsTrait;
use Chubbyphp\Serialization\Mapping\CallableNormalizationObjectMapping;
use Chubbyphp\Serialization\Mapping\NormalizationObjectMappingInterface;
use PHPUnit\Framework\TestCase;
use Pimple\Container;
use Slim\Interfaces\RouteParserInterface;

/**
 * @covers \App\ServiceProvider\SerializationServiceProvider
 *
 * @internal
 */
final class SerializationServiceProviderTest extends TestCase
{
    use MockByCallsTrait;

    public function testRegister(): void
    {
        $container = new Container([
            'router' => $this->getMockByCalls(RouteParserInterface::class),
        ]);

        $serviceProvider = new SerializationServiceProvider();
        $serviceProvider->register($container);

        self::assertArrayHasKey('serializer.normalizer.objectmappings', $container);

        $mappings = $container['serializer.normalizer.objectmappings'];

        self::assertCount(7, $mappings);

        self::assertInstanceOf(CallableNormalizationObjectMapping::class, $mappings[0]);
        self::assertInstanceOf(CallableNormalizationObjectMapping::class, $mappings[1]);
        self::assertInstanceOf(CallableNormalizationObjectMapping::class, $mappings[2]);
        self::assertInstanceOf(CallableNormalizationObjectMapping::class, $mappings[3]);
        self::assertInstanceOf(CallableNormalizationObjectMapping::class, $mappings[4]);
        self::assertInstanceOf(CallableNormalizationObjectMapping::class, $mappings[5]);
        self::assertInstanceOf(CallableNormalizationObjectMapping::class, $mappings[6]);

        self::assertCount(5, $mappings[0]->getNormalizationFieldMappings('path'));
        self::assertCount(6, $mappings[1]->getNormalizationFieldMappings('path'));
        self::assertCount(4, $mappings[2]->getNormalizationFieldMappings('path'));
        self::assertCount(5, $mappings[3]->getNormalizationFieldMappings('path'));
        self::assertCount(5, $mappings[4]->getNormalizationFieldMappings('path'));
        self::assertCount(5, $mappings[5]->getNormalizationFieldMappings('path'));
        self::assertCount(6, $mappings[6]->getNormalizationFieldMappings('path'));
    }

    public function testMappings(): void
    {
        $container = new Container([
            'sampleService' => function () {
                return new \stdClass();
            },
        ]);

        $serviceProvider = new SerializationServiceProvider();
        $serviceProvider->register($container);

        $stdClassMapping = new class(new \stdClass()) implements NormalizationObjectMappingInterface {
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
             * @return string|null
             */
            public function getNormalizationType()
            {
                return 'stdClass';
            }

            /**
             * @param string $path
             *
             * @return NormalizationFieldMappingInterface[]
             */
            public function getNormalizationFieldMappings(string $path): array
            {
                return [];
            }

            /**
             * @param string $path
             *
             * @return NormalizationFieldMappingInterface[]
             */
            public function getNormalizationEmbeddedFieldMappings(string $path): array
            {
                return [];
            }

            /**
             * @param string $path
             *
             * @return NormalizationLinkMappingInterface[]
             */
            public function getNormalizationLinkMappings(string $path): array
            {
                return [];
            }
        };

        $stdClassMappingClass = get_class($stdClassMapping);

        $mappingConfigs = [];
        $mappingConfigs[\stdClass::class] = new MappingConfig($stdClassMappingClass, ['sampleService']);

        $container['serializer.mappingConfigs'] = $mappingConfigs;

        /** @var CallableNormalizationObjectMapping[] $mappings */
        $mappings = $container['serializer.normalizer.objectmappings'];

        self::assertCount(1, $mappings);

        self::assertInstanceOf(CallableNormalizationObjectMapping::class, $mappings[0]);

        self::assertCount(0, $mappings[0]->getNormalizationFieldMappings('path'));
    }
}
