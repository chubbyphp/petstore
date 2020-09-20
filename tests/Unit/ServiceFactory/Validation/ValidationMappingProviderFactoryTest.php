<?php

declare(strict_types=1);

namespace App\Tests\Unit\ServiceFactory\Validation;

use App\Mapping\Validation\PetCollectionMapping;
use App\Mapping\Validation\PetMapping;
use App\Mapping\Validation\VaccinationMapping;
use App\ServiceFactory\Validation\ValidationMappingProviderFactory;
use Chubbyphp\Mock\MockByCallsTrait;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;

/**
 * @covers \App\ServiceFactory\Validation\ValidationMappingProviderFactory
 *
 * @internal
 */
final class ValidationMappingProviderFactoryTest extends TestCase
{
    use MockByCallsTrait;

    public function testInvoke(): void
    {
        /** @var ContainerInterface $container */
        $container = $this->getMockByCalls(ContainerInterface::class);

        $factory = new ValidationMappingProviderFactory();

        $service = $factory($container);

        self::assertEquals([
            new PetCollectionMapping(),
            new PetMapping(),
            new VaccinationMapping(),
        ], $service);
    }
}
