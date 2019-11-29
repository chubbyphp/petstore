<?php

declare(strict_types=1);

namespace App\Tests\Unit\ServiceFactory;

use App\Collection\PetCollection;
use App\Mapping\Deserialization\PetCollectionMapping;
use App\Mapping\Deserialization\PetMapping;
use App\Mapping\MappingConfig;
use App\Model\Pet;
use App\ServiceFactory\DeserializationServiceFactory;
use Chubbyphp\Deserialization\Decoder\JsonTypeDecoder;
use Chubbyphp\Deserialization\Decoder\JsonxTypeDecoder;
use Chubbyphp\Deserialization\Decoder\UrlEncodedTypeDecoder;
use Chubbyphp\Deserialization\Decoder\YamlTypeDecoder;
use Chubbyphp\Deserialization\Mapping\CallableDenormalizationObjectMapping;
use Chubbyphp\Mock\Call;
use Chubbyphp\Mock\MockByCallsTrait;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;

/**
 * @covers \App\ServiceFactory\DeserializationServiceFactory
 *
 * @internal
 */
final class DeserializationServiceFactoryTest extends TestCase
{
    use MockByCallsTrait;

    public function testFactories(): void
    {
        $factories = (new DeserializationServiceFactory())();

        self::assertCount(3, $factories);
    }

    public function testDecoderTypes(): void
    {
        $factories = (new DeserializationServiceFactory())();

        self::assertArrayHasKey('deserializer.decodertypes', $factories);

        $decoderTypes = $factories['deserializer.decodertypes']();

        self::assertIsArray($decoderTypes);

        self::assertCount(4, $decoderTypes);

        self::assertInstanceOf(JsonTypeDecoder::class, array_shift($decoderTypes));
        self::assertInstanceOf(JsonxTypeDecoder::class, array_shift($decoderTypes));
        self::assertInstanceOf(UrlEncodedTypeDecoder::class, array_shift($decoderTypes));
        self::assertInstanceOf(YamlTypeDecoder::class, array_shift($decoderTypes));
    }

    public function testMappingConfigs(): void
    {
        $factories = (new DeserializationServiceFactory())();

        self::assertArrayHasKey('deserializer.mappingConfigs', $factories);

        $mappingConfigs = $factories['deserializer.mappingConfigs']();

        self::assertIsArray($mappingConfigs);

        self::assertCount(2, $mappingConfigs);

        self::assertMappingConfig($mappingConfigs, PetCollection::class, PetCollectionMapping::class);
        self::assertMappingConfig($mappingConfigs, Pet::class, PetMapping::class);
    }

    public function testObjectMappings(): void
    {
        /** @var ContainerInterface|MockObject $container */
        $container = $this->getMockByCalls(ContainerInterface::class, [
            Call::create('get')->with('deserializer.mappingConfigs')->willReturn([
                Pet::class => new MappingConfig(PetMapping::class, ['dependencyClass']),
            ]),
            Call::create('get')->with('dependencyClass')->willReturn(new \stdClass()),
        ]);

        $factories = (new DeserializationServiceFactory())();

        self::assertArrayHasKey('deserializer.denormalizer.objectmappings', $factories);

        $mappings = $factories['deserializer.denormalizer.objectmappings']($container);

        self::assertIsArray($mappings);

        self::assertCount(1, $mappings);

        /** @var CallableDenormalizationObjectMapping $mapping */
        $mapping = array_shift($mappings);

        self::assertInstanceOf(CallableDenormalizationObjectMapping::class, $mapping);

        $mapping->getDenormalizationFactory('path');
    }

    /**
     * @param array<string, MappingConfig> $mappingConfigs
     */
    private function assertMappingConfig(
        array $mappingConfigs,
        string $class,
        string $mappingClass,
        array $dependencies = []
    ): void {
        self::assertArrayHasKey($class, $mappingConfigs);

        /** @var MappingConfig $mappingConfig */
        $mappingConfig = $mappingConfigs[$class];

        self::assertInstanceOf(MappingConfig::class, $mappingConfig);

        self::assertSame($mappingClass, $mappingConfig->getMappingClass());
        self::assertSame($dependencies, $mappingConfig->getDependencies());
    }
}
