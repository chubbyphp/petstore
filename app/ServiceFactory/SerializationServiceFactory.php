<?php

declare(strict_types=1);

namespace App\ServiceFactory;

use App\Collection\PetCollection;
use App\Mapping\Serialization\PetCollectionMapping;
use App\Mapping\Serialization\PetMapping;
use App\Mapping\Serialization\VaccinationMapping;
use App\Model\Pet;
use App\Model\Vaccination;
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
use Chubbyphp\Framework\Router\RouterInterface;
use Chubbyphp\Serialization\Encoder\JsonTypeEncoder;
use Chubbyphp\Serialization\Encoder\JsonxTypeEncoder;
use Chubbyphp\Serialization\Encoder\UrlEncodedTypeEncoder;
use Chubbyphp\Serialization\Encoder\YamlTypeEncoder;
use Chubbyphp\Serialization\Mapping\LazyNormalizationObjectMapping;
use Psr\Container\ContainerInterface;

final class SerializationServiceFactory
{
    /**
     * @return array<string, callable>
     */
    public function __invoke(): array
    {
        return [
            'serializer.encodertypes' => static function () {
                $encoderTypes = [];

                $encoderTypes[] = new JsonTypeEncoder();
                $encoderTypes[] = new JsonxTypeEncoder(false, 'application/jsonx+xml');
                $encoderTypes[] = new UrlEncodedTypeEncoder();
                $encoderTypes[] = new YamlTypeEncoder();

                return $encoderTypes;
            },
            BadRequestMapping::class => static function () {
                return new BadRequestMapping();
            },
            InternalServerErrorMapping::class => static function () {
                return new InternalServerErrorMapping();
            },
            NotAcceptableMapping::class => static function () {
                return new NotAcceptableMapping();
            },
            NotFoundMapping::class => static function () {
                return new NotFoundMapping();
            },
            PetCollectionMapping::class => static function (ContainerInterface $container) {
                return new PetCollectionMapping($container->get(RouterInterface::class));
            },
            PetMapping::class => static function (ContainerInterface $container) {
                return new PetMapping($container->get(RouterInterface::class));
            },
            UnprocessableEntityMapping::class => static function () {
                return new UnprocessableEntityMapping();
            },
            UnsupportedMediaTypeMapping::class => static function () {
                return new UnsupportedMediaTypeMapping();
            },
            VaccinationMapping::class => static function () {
                return new VaccinationMapping();
            },
            'serializer.normalizer.objectmappings' => static function (ContainerInterface $container) {
                return [
                    new LazyNormalizationObjectMapping($container, BadRequestMapping::class, BadRequest::class),
                    new LazyNormalizationObjectMapping($container, InternalServerErrorMapping::class, InternalServerError::class),
                    new LazyNormalizationObjectMapping($container, NotAcceptableMapping::class, NotAcceptable::class),
                    new LazyNormalizationObjectMapping($container, NotFoundMapping::class, NotFound::class),
                    new LazyNormalizationObjectMapping($container, PetCollectionMapping::class, PetCollection::class),
                    new LazyNormalizationObjectMapping($container, PetMapping::class, Pet::class),
                    new LazyNormalizationObjectMapping($container, UnprocessableEntityMapping::class, UnprocessableEntity::class),
                    new LazyNormalizationObjectMapping($container, UnsupportedMediaTypeMapping::class, UnsupportedMediaType::class),
                    new LazyNormalizationObjectMapping($container, VaccinationMapping::class, Vaccination::class),
                ];
            },
        ];
    }
}
