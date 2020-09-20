<?php

declare(strict_types=1);

namespace App\Tests\Unit\ServiceFactory\Deserialization;

use App\Mapping\Deserialization\PetCollectionMapping;
use App\Mapping\Deserialization\PetMapping;
use App\Mapping\Deserialization\VaccinationMapping;
use App\ServiceFactory\Deserialization\DenormalizationObjectMappingsFactory;
use Chubbyphp\Mock\MockByCallsTrait;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;

/**
 * @covers \App\ServiceFactory\Deserialization\DenormalizationObjectMappingsFactory
 *
 * @internal
 */
final class DenormalizationObjectMappingsFactoryTest extends TestCase
{
    use MockByCallsTrait;

    public function testInvoke(): void
    {
        /** @var ContainerInterface $container */
        $container = $this->getMockByCalls(ContainerInterface::class);

        $factory = new DenormalizationObjectMappingsFactory();

        $service = $factory($container);

        self::assertEquals([
            new PetCollectionMapping(),
            new PetMapping(),
            new VaccinationMapping(),
        ], $service);
    }
}
