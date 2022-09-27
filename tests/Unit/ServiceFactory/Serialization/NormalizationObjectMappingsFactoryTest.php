<?php

declare(strict_types=1);

namespace App\Tests\Unit\ServiceFactory\Serialization;

use App\Mapping\Serialization\PetCollectionMapping;
use App\Mapping\Serialization\PetMapping;
use App\Mapping\Serialization\VaccinationMapping;
use App\ServiceFactory\Serialization\NormalizationObjectMappingsFactory;
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
            new PetCollectionMapping($urlGenerator),
            new PetMapping($urlGenerator),
            new VaccinationMapping(),
        ], $service);
    }
}
