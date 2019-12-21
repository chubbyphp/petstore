<?php

declare(strict_types=1);

namespace App\Tests\Unit\ServiceFactory;

use App\Collection\PetCollection;
use App\Mapping\MappingConfig;
use App\Mapping\Serialization\PetCollectionMapping;
use App\Mapping\Serialization\PetMapping;
use App\Mapping\Serialization\VaccinationMapping;
use App\Model\Pet;
use App\Model\Vaccination;
use App\ServiceFactory\SerializationServiceFactory;
use Chubbyphp\ApiHttp\ApiProblem\ClientError\BadRequest;
use Chubbyphp\ApiHttp\ApiProblem\ClientError\NotAcceptable;
use Chubbyphp\ApiHttp\ApiProblem\ClientError\NotFound;
use Chubbyphp\ApiHttp\ApiProblem\ClientError\UnprocessableEntity;
use Chubbyphp\ApiHttp\ApiProblem\ClientError\UnsupportedMediaType;
use Chubbyphp\ApiHttp\Serialization\ApiProblem\ClientError\BadRequestMapping;
use Chubbyphp\ApiHttp\Serialization\ApiProblem\ClientError\NotAcceptableMapping;
use Chubbyphp\ApiHttp\Serialization\ApiProblem\ClientError\NotFoundMapping;
use Chubbyphp\ApiHttp\Serialization\ApiProblem\ClientError\UnprocessableEntityMapping;
use Chubbyphp\ApiHttp\Serialization\ApiProblem\ClientError\UnsupportedMediaTypeMapping;
use Chubbyphp\Framework\Router\RouterInterface;
use Chubbyphp\Mock\Call;
use Chubbyphp\Mock\MockByCallsTrait;
use Chubbyphp\Serialization\Encoder\JsonTypeEncoder;
use Chubbyphp\Serialization\Encoder\JsonxTypeEncoder;
use Chubbyphp\Serialization\Encoder\UrlEncodedTypeEncoder;
use Chubbyphp\Serialization\Encoder\YamlTypeEncoder;
use Chubbyphp\Serialization\Mapping\CallableNormalizationObjectMapping;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;

/**
 * @covers \App\ServiceFactory\SerializationServiceFactory
 *
 * @internal
 */
final class SerializationServiceFactoryTest extends TestCase
{
    use MockByCallsTrait;

    public function testFactories(): void
    {
        $factories = (new SerializationServiceFactory())();

        self::assertCount(3, $factories);
    }

    public function testEncoderTypes(): void
    {
        $factories = (new SerializationServiceFactory())();

        self::assertArrayHasKey('serializer.encodertypes', $factories);

        $encoderTypes = $factories['serializer.encodertypes']();

        self::assertIsArray($encoderTypes);

        self::assertCount(4, $encoderTypes);

        self::assertInstanceOf(JsonTypeEncoder::class, array_shift($encoderTypes));

        $jsonxTypeEncoder = array_shift($encoderTypes);

        self::assertInstanceOf(JsonxTypeEncoder::class, $jsonxTypeEncoder);

        $prettyPrintReflection = new \ReflectionProperty($jsonxTypeEncoder, 'prettyPrint');
        $prettyPrintReflection->setAccessible(true);

        self::assertSame(false, $prettyPrintReflection->getValue($jsonxTypeEncoder));

        $contentTypeReflection = new \ReflectionProperty($jsonxTypeEncoder, 'contentType');
        $contentTypeReflection->setAccessible(true);

        self::assertSame('application/jsonx+xml', $contentTypeReflection->getValue($jsonxTypeEncoder));

        self::assertInstanceOf(UrlEncodedTypeEncoder::class, array_shift($encoderTypes));
        self::assertInstanceOf(YamlTypeEncoder::class, array_shift($encoderTypes));
    }

    public function testMappingConfigs(): void
    {
        $factories = (new SerializationServiceFactory())();

        self::assertArrayHasKey('serializer.mappingConfigs', $factories);

        $mappingConfigs = $factories['serializer.mappingConfigs']();

        self::assertIsArray($mappingConfigs);

        self::assertCount(8, $mappingConfigs);

        self::assertMappingConfig($mappingConfigs, BadRequest::class, BadRequestMapping::class);
        self::assertMappingConfig($mappingConfigs, NotAcceptable::class, NotAcceptableMapping::class);
        self::assertMappingConfig($mappingConfigs, NotFound::class, NotFoundMapping::class);
        self::assertMappingConfig($mappingConfigs, Pet::class, PetMapping::class, [RouterInterface::class]);
        self::assertMappingConfig(
            $mappingConfigs,
            PetCollection::class,
            PetCollectionMapping::class,
            [RouterInterface::class]
        );
        self::assertMappingConfig($mappingConfigs, UnprocessableEntity::class, UnprocessableEntityMapping::class);
        self::assertMappingConfig($mappingConfigs, UnsupportedMediaType::class, UnsupportedMediaTypeMapping::class);
        self::assertMappingConfig($mappingConfigs, Vaccination::class, VaccinationMapping::class);
    }

    public function testObjectMappings(): void
    {
        /** @var RouterInterface|MockObject $router */
        $router = $this->getMockByCalls(RouterInterface::class);

        /** @var ContainerInterface|MockObject $container */
        $container = $this->getMockByCalls(ContainerInterface::class, [
            Call::create('get')->with('serializer.mappingConfigs')->willReturn([
                Pet::class => new MappingConfig(PetMapping::class, [RouterInterface::class]),
            ]),
            Call::create('get')->with(RouterInterface::class)->willReturn($router),
        ]);

        $factories = (new SerializationServiceFactory())();

        self::assertArrayHasKey('serializer.normalizer.objectmappings', $factories);

        $mappings = $factories['serializer.normalizer.objectmappings']($container);

        self::assertIsArray($mappings);

        self::assertCount(1, $mappings);

        /** @var CallableNormalizationObjectMapping $mapping */
        $mapping = array_shift($mappings);

        self::assertInstanceOf(CallableNormalizationObjectMapping::class, $mapping);

        $mapping->getNormalizationType();
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
