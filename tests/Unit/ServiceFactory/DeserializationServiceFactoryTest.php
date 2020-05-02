<?php

declare(strict_types=1);

namespace App\Tests\Unit\ServiceFactory;

use App\Collection\PetCollection;
use App\Mapping\Deserialization\PetCollectionMapping;
use App\Mapping\Deserialization\PetMapping;
use App\Mapping\Deserialization\VaccinationMapping;
use App\Model\Pet;
use App\Model\Vaccination;
use App\ServiceFactory\DeserializationServiceFactory;
use Chubbyphp\Container\Container;
use Chubbyphp\Deserialization\Decoder\JsonTypeDecoder;
use Chubbyphp\Deserialization\Decoder\JsonxTypeDecoder;
use Chubbyphp\Deserialization\Decoder\UrlEncodedTypeDecoder;
use Chubbyphp\Deserialization\Decoder\YamlTypeDecoder;
use Chubbyphp\Deserialization\Mapping\LazyDenormalizationObjectMapping;
use PHPUnit\Framework\TestCase;

/**
 * @covers \App\ServiceFactory\DeserializationServiceFactory
 *
 * @internal
 */
final class DeserializationServiceFactoryTest extends TestCase
{
    public function testFactories(): void
    {
        $factories = (new DeserializationServiceFactory())();

        self::assertCount(5, $factories);
    }

    public function testDecoderTypes(): void
    {
        $factories = (new DeserializationServiceFactory())();

        self::assertArrayHasKey('deserializer.decodertypes', $factories);

        $decoderTypes = $factories['deserializer.decodertypes']();

        self::assertIsArray($decoderTypes);

        self::assertCount(4, $decoderTypes);

        self::assertInstanceOf(JsonTypeDecoder::class, array_shift($decoderTypes));

        $jsonxTypeDecoder = array_shift($decoderTypes);

        self::assertInstanceOf(JsonxTypeDecoder::class, $jsonxTypeDecoder);

        $contentTypeReflection = new \ReflectionProperty($jsonxTypeDecoder, 'contentType');
        $contentTypeReflection->setAccessible(true);

        self::assertSame('application/jsonx+xml', $contentTypeReflection->getValue($jsonxTypeDecoder));

        self::assertInstanceOf(UrlEncodedTypeDecoder::class, array_shift($decoderTypes));
        self::assertInstanceOf(YamlTypeDecoder::class, array_shift($decoderTypes));
    }

    public function testObjectMappings(): void
    {
        $expectedMappings = [
            PetCollectionMapping::class => PetCollection::class,
            PetMapping::class => Pet::class,
            VaccinationMapping::class => Vaccination::class,
        ];

        $factories = (new DeserializationServiceFactory())();

        $container = new Container();
        $container->factory(
            'deserializer.denormalizer.objectmappings',
            $factories['deserializer.denormalizer.objectmappings']
        );

        foreach ($expectedMappings as $mappingClass => $class) {
            $container->factory($mappingClass, $factories[$mappingClass]);
        }

        self::assertArrayHasKey('deserializer.denormalizer.objectmappings', $factories);

        $mappings = $container->get('deserializer.denormalizer.objectmappings');

        self::assertIsArray($mappings);

        self::assertCount(count($expectedMappings), $mappings);

        foreach ($expectedMappings as $mappingClass => $class) {
            /** @var LazyDenormalizationObjectMapping $mapping */
            $mapping = array_shift($mappings);

            self::assertInstanceOf(LazyDenormalizationObjectMapping::class, $mapping);

            self::assertSame($class, $mapping->getClass());

            $mapping->getDenormalizationFieldMappings('path');
        }
    }
}
