<?php

declare(strict_types=1);

namespace App\Tests\Unit\ServiceFactory;

use App\Collection\PetCollection;
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
use Chubbyphp\ApiHttp\ApiProblem\ServerError\InternalServerError;
use Chubbyphp\ApiHttp\Serialization\ApiProblem\ClientError\BadRequestMapping;
use Chubbyphp\ApiHttp\Serialization\ApiProblem\ClientError\NotAcceptableMapping;
use Chubbyphp\ApiHttp\Serialization\ApiProblem\ClientError\NotFoundMapping;
use Chubbyphp\ApiHttp\Serialization\ApiProblem\ClientError\UnprocessableEntityMapping;
use Chubbyphp\ApiHttp\Serialization\ApiProblem\ClientError\UnsupportedMediaTypeMapping;
use Chubbyphp\ApiHttp\Serialization\ApiProblem\ServerError\InternalServerErrorMapping;
use Chubbyphp\Container\Container;
use Chubbyphp\Framework\Router\RouterInterface;
use Chubbyphp\Mock\MockByCallsTrait;
use Chubbyphp\Serialization\Encoder\JsonTypeEncoder;
use Chubbyphp\Serialization\Encoder\JsonxTypeEncoder;
use Chubbyphp\Serialization\Encoder\UrlEncodedTypeEncoder;
use Chubbyphp\Serialization\Encoder\YamlTypeEncoder;
use Chubbyphp\Serialization\Mapping\LazyNormalizationObjectMapping;
use PHPUnit\Framework\TestCase;

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

        self::assertCount(11, $factories);
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

    public function testObjectMappings(): void
    {
        $expectedMappings = [
            BadRequestMapping::class => BadRequest::class,
            InternalServerErrorMapping::class => InternalServerError::class,
            NotAcceptableMapping::class => NotAcceptable::class,
            NotFoundMapping::class => NotFound::class,
            PetCollectionMapping::class => PetCollection::class,
            PetMapping::class => Pet::class,
            UnprocessableEntityMapping::class => UnprocessableEntity::class,
            UnsupportedMediaTypeMapping::class => UnsupportedMediaType::class,
            VaccinationMapping::class => Vaccination::class,
        ];

        $factories = (new SerializationServiceFactory())();

        $container = new Container();
        $container->factory(RouterInterface::class, function () {
            return $this->getMockByCalls(RouterInterface::class);
        });

        $container->factory('serializer.normalizer.objectmappings', $factories['serializer.normalizer.objectmappings']);

        foreach ($expectedMappings as $mappingClass => $class) {
            $container->factory($mappingClass, $factories[$mappingClass]);
        }

        self::assertArrayHasKey('serializer.normalizer.objectmappings', $factories);

        $mappings = $container->get('serializer.normalizer.objectmappings');

        self::assertIsArray($mappings);

        self::assertCount(count($expectedMappings), $mappings);

        foreach ($expectedMappings as $mappingClass => $class) {
            /** @var LazyNormalizationObjectMapping $mapping */
            $mapping = array_shift($mappings);

            self::assertInstanceOf(LazyNormalizationObjectMapping::class, $mapping);

            self::assertSame($class, $mapping->getClass());

            $mapping->getNormalizationFieldMappings('path');
        }
    }
}
