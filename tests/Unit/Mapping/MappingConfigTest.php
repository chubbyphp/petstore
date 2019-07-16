<?php

declare(strict_types=1);

namespace App\Tests\Unit\Mapping;

use App\Mapping\MappingConfig;
use PHPUnit\Framework\TestCase;

/**
 * @covers \App\Mapping\MappingConfig
 *
 * @internal
 */
final class MappingConfigTest extends TestCase
{
    public function testMapping(): void
    {
        $mappingConfig = new MappingConfig(\stdClass::class, ['service1', 'service2']);

        self::assertSame(\stdClass::class, $mappingConfig->getMappingClass());
        self::assertSame(['service1', 'service2'], $mappingConfig->getDependencies());
    }
}
