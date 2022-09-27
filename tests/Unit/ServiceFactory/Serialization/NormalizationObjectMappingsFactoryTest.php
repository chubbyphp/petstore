<?php

declare(strict_types=1);

namespace App\Tests\Unit\ServiceFactory\Serialization;

use App\Mapping\Serialization\PetCollectionMapping;
use App\Mapping\Serialization\PetMapping;
use App\Mapping\Serialization\VaccinationMapping;
use App\ServiceFactory\Serialization\NormalizationObjectMappingsFactory;
use Chubbyphp\Mock\Call;
use Chubbyphp\Mock\MockByCallsTrait;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;
use Slim\Interfaces\RouteParserInterface;

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
        /** @var RouteParserInterface $router */
        $router = $this->getMockByCalls(RouteParserInterface::class);

        /** @var ContainerInterface $container */
        $container = $this->getMockByCalls(ContainerInterface::class, [
            Call::create('get')->with(RouteParserInterface::class)->willReturn($router),
        ]);

        $factory = new NormalizationObjectMappingsFactory();

        $service = $factory($container);

        self::assertEquals([
            new PetCollectionMapping($router),
            new PetMapping($router),
            new VaccinationMapping(),
        ], $service);
    }
}
