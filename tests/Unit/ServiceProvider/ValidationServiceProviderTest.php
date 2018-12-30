<?php

declare(strict_types=1);

namespace App\Tests\Unit\ServiceProvider;

use App\Mapping\MappingConfig;
use App\Mapping\Validation\PetMapping;
use App\Model\Pet;
use App\ServiceProvider\ValidationServiceProvider;
use Chubbyphp\Validation\Mapping\CallableValidationMappingProvider;
use PHPUnit\Framework\TestCase;
use Pimple\Container;

/**
 * @covers \App\ServiceProvider\ValidationServiceProvider
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

        self::assertArrayHasKey('validator.mappingConfigs', $container);

        $mappingConfigs = $container['validator.mappingConfigs'];

        // hack to tests dependencies
        $mappingConfigs[Pet::class] = new MappingConfig(PetMapping::class, ['someService']);

        $container['validator.mappingConfigs'] = $mappingConfigs;

        self::assertArrayHasKey('validator.mappings', $container);

        $mappings = $container['validator.mappings'];

        self::assertCount(2, $mappings);

        self::assertInstanceOf(CallableValidationMappingProvider::class, $mappings[0]);
        self::assertInstanceOf(CallableValidationMappingProvider::class, $mappings[1]);

        self::assertCount(3, $mappings[0]->getValidationPropertyMappings('path'));
        self::assertCount(2, $mappings[1]->getValidationPropertyMappings('path'));
    }
}
