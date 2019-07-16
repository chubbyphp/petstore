<?php

declare(strict_types=1);

namespace App\Tests\Unit\ServiceProvider;

use App\Mapping\MappingConfig;
use App\ServiceProvider\ValidationServiceProvider;
use Chubbyphp\Validation\Mapping\CallableValidationMappingProvider;
use Chubbyphp\Validation\Mapping\ValidationMappingProviderInterface;
use PHPUnit\Framework\TestCase;
use Pimple\Container;

/**
 * @covers \App\ServiceProvider\ValidationServiceProvider
 *
 * @internal
 */
final class ValidationServiceProviderTest extends TestCase
{
    public function testRegister(): void
    {
        $container = new Container([
            'someService' => function () {
                return new \stdClass();
            },
        ]);

        $serviceProvider = new ValidationServiceProvider();
        $serviceProvider->register($container);

        self::assertArrayHasKey('validator.mappings', $container);

        $mappings = $container['validator.mappings'];

        self::assertCount(2, $mappings);

        self::assertInstanceOf(CallableValidationMappingProvider::class, $mappings[0]);
        self::assertInstanceOf(CallableValidationMappingProvider::class, $mappings[1]);

        self::assertCount(3, $mappings[0]->getValidationPropertyMappings('path'));
        self::assertCount(2, $mappings[1]->getValidationPropertyMappings('path'));
    }

    public function testMappings(): void
    {
        $container = new Container([
            'sampleService' => function () {
                return new \stdClass();
            },
        ]);

        $serviceProvider = new ValidationServiceProvider();
        $serviceProvider->register($container);

        $stdClassMapping = new class(new \stdClass()) implements ValidationMappingProviderInterface {
            public function __construct(\stdClass $sampleService)
            {
            }

            /**
             * @return string
             */
            public function getClass(): string
            {
                return \stdClass::class;
            }

            /**
             * @param string $path
             *
             * @return ValidationClassMappingInterface|null
             */
            public function getValidationClassMapping(string $path)
            {
            }

            /**
             * @param string $path
             *
             * @return ValidationPropertyMappingInterface[]
             */
            public function getValidationPropertyMappings(string $path): array
            {
                return [];
            }
        };

        $stdClassMappingClass = get_class($stdClassMapping);

        $mappingConfigs = [];
        $mappingConfigs[\stdClass::class] = new MappingConfig($stdClassMappingClass, ['sampleService']);

        $container['validator.mappingConfigs'] = $mappingConfigs;

        /** @var CallableValidationMappingProvider[] $mappings */
        $mappings = $container['validator.mappings'];

        self::assertCount(1, $mappings);

        self::assertInstanceOf(CallableValidationMappingProvider::class, $mappings[0]);

        self::assertCount(0, $mappings[0]->getValidationPropertyMappings('path'));
    }
}
