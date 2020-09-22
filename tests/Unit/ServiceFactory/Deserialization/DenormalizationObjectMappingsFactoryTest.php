<?php

declare(strict_types=1);

namespace App\Tests\Unit\ServiceFactory\Deserialization;

use App\Mapping\Deserialization\PetCollectionMapping;
use App\Mapping\Deserialization\PetMapping;
use App\Mapping\Deserialization\VaccinationMapping;
use App\ServiceFactory\Deserialization\DenormalizationObjectMappingsFactory;
use Chubbyphp\Mock\MockByCallsTrait;
use PHPUnit\Framework\TestCase;

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
        $factory = new DenormalizationObjectMappingsFactory();

        $service = $factory();

        self::assertEquals([
            new PetCollectionMapping(),
            new PetMapping(),
            new VaccinationMapping(),
        ], $service);
    }
}
