<?php

declare(strict_types=1);

namespace App\ServiceFactory\Serialization;

use App\Mapping\Serialization\PetCollectionMapping;
use App\Mapping\Serialization\PetMapping;
use App\Mapping\Serialization\VaccinationMapping;
use Chubbyphp\ApiHttp\Serialization\ApiProblem\ClientError\BadRequestMapping;
use Chubbyphp\ApiHttp\Serialization\ApiProblem\ClientError\NotAcceptableMapping;
use Chubbyphp\ApiHttp\Serialization\ApiProblem\ClientError\NotFoundMapping;
use Chubbyphp\ApiHttp\Serialization\ApiProblem\ClientError\UnprocessableEntityMapping;
use Chubbyphp\ApiHttp\Serialization\ApiProblem\ClientError\UnsupportedMediaTypeMapping;
use Chubbyphp\ApiHttp\Serialization\ApiProblem\ServerError\InternalServerErrorMapping;
use Chubbyphp\Framework\Router\RouterInterface;
use Chubbyphp\Serialization\Mapping\NormalizationObjectMappingInterface;
use Psr\Container\ContainerInterface;

final class NormalizationObjectMappingsFactory
{
    /**
     * @return array<int, NormalizationObjectMappingInterface>
     */
    public function __invoke(ContainerInterface $container): array
    {
        /** @var RouterInterface $router */
        $router = $container->get(RouterInterface::class);

        return [
            new BadRequestMapping(),
            new InternalServerErrorMapping(),
            new NotAcceptableMapping(),
            new NotFoundMapping(),
            new PetCollectionMapping($router),
            new PetMapping($router),
            new UnprocessableEntityMapping(),
            new UnsupportedMediaTypeMapping(),
            new VaccinationMapping(),
        ];
    }
}
