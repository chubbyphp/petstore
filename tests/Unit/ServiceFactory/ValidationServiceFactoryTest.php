<?php

declare(strict_types=1);

namespace App\Tests\Unit\ServiceFactory;

use App\Collection\PetCollection;
use App\Mapping\MappingConfig;
use App\Mapping\Validation\PetCollectionMapping;
use App\Mapping\Validation\PetMapping;
use App\Model\Pet;
use App\ServiceFactory\ValidationServiceFactory;
use Chubbyphp\Mock\Call;
use Chubbyphp\Mock\MockByCallsTrait;
use Chubbyphp\Validation\Mapping\CallableValidationMappingProvider;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;

/**
 * @covers \App\ServiceFactory\ValidationServiceFactory
 *
 * @internal
 */
final class ValidationServiceFactoryTest extends TestCase
{
    use MockByCallsTrait;

    public function testFactories(): void
    {
        $factories = (new ValidationServiceFactory())();

        self::assertCount(2, $factories);
    }

    public function testMappingConfigs(): void
    {
        $factories = (new ValidationServiceFactory())();

        self::assertArrayHasKey('validator.mappingConfigs', $factories);

        $mappingConfigs = $factories['validator.mappingConfigs']();

        self::assertIsArray($mappingConfigs);

        self::assertCount(2, $mappingConfigs);

        self::assertMappingConfig($mappingConfigs, PetCollection::class, PetCollectionMapping::class);
        self::assertMappingConfig($mappingConfigs, Pet::class, PetMapping::class);
    }

    public function testObjectMappings(): void
    {
        /** @var ContainerInterface|MockObject $container */
        $container = $this->getMockByCalls(ContainerInterface::class, [
            Call::create('get')->with('validator.mappingConfigs')->willReturn([
                Pet::class => new MappingConfig(PetMapping::class, ['dependencyClass']),
            ]),
            Call::create('get')->with('dependencyClass')->willReturn(new \stdClass()),
        ]);

        $factories = (new ValidationServiceFactory())();

        self::assertArrayHasKey('validator.mappings', $factories);

        $mappings = $factories['validator.mappings']($container);

        self::assertIsArray($mappings);

        self::assertCount(1, $mappings);

        /** @var CallableValidationMappingProvider $mapping */
        $mapping = array_shift($mappings);

        self::assertInstanceOf(CallableValidationMappingProvider::class, $mapping);

        $mapping->getValidationClassMapping('path');
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
