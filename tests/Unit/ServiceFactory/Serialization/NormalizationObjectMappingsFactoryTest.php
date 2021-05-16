<?php

declare(strict_types=1);

namespace App\Tests\Unit\ServiceFactory\Serialization;

use App\Mapping\Serialization\PetCollectionMapping;
use App\Mapping\Serialization\PetMapping;
use App\Mapping\Serialization\VaccinationMapping;
use App\ServiceFactory\Serialization\NormalizationObjectMappingsFactory;
use Chubbyphp\ApiHttp\Serialization\ApiProblem\ClientError\BadRequestMapping;
use Chubbyphp\ApiHttp\Serialization\ApiProblem\ClientError\NotAcceptableMapping;
use Chubbyphp\ApiHttp\Serialization\ApiProblem\ClientError\NotFoundMapping;
use Chubbyphp\ApiHttp\Serialization\ApiProblem\ClientError\UnprocessableEntityMapping;
use Chubbyphp\ApiHttp\Serialization\ApiProblem\ClientError\UnsupportedMediaTypeMapping;
use Chubbyphp\ApiHttp\Serialization\ApiProblem\ServerError\InternalServerErrorMapping;
use Chubbyphp\Framework\Router\UrlGeneratorInterface;
use Chubbyphp\Mock\Call;
use Chubbyphp\Mock\MockByCallsTrait;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;

/**
 * @covers \App\ServiceFactory\Serialization\NormalizationObjectMappingsFactory
 *
 * @internal
 */
final class NormalizationObjectMappingsFactoryTest extends TestCase
{
    use MockByCallsTrait;

    public function testInvoke(): void
    {
        /** @var UrlGeneratorInterface $urlGenerator */
        $urlGenerator = $this->getMockByCalls(UrlGeneratorInterface::class);

        /** @var ContainerInterface $container */
        $container = $this->getMockByCalls(ContainerInterface::class, [
            Call::create('get')->with(UrlGeneratorInterface::class)->willReturn($urlGenerator),
        ]);

        $factory = new NormalizationObjectMappingsFactory();

        $service = $factory($container);

        self::assertEquals([
            new BadRequestMapping(),
            new InternalServerErrorMapping(),
            new NotAcceptableMapping(),
            new NotFoundMapping(),
            new PetCollectionMapping($urlGenerator),
            new PetMapping($urlGenerator),
            new UnprocessableEntityMapping(),
            new UnsupportedMediaTypeMapping(),
            new VaccinationMapping(),
        ], $service);
    }
}
