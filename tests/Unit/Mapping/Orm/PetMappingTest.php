<?php

declare(strict_types=1);

namespace App\Tests\Unit\Mapping\Orm;

use App\Mapping\Orm\PetMapping;
use Chubbyphp\Mock\Call;
use Chubbyphp\Mock\MockByCallsTrait;
use Doctrine\ORM\Mapping\ClassMetadata;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * @covers \App\Mapping\Orm\PetMapping
 *
 * @internal
 */
final class PetMappingTest extends TestCase
{
    use MockByCallsTrait;

    public function testGetClass(): void
    {
        /** @var ClassMetadata|MockObject $classMetadata */
        $classMetadata = $this->getMockByCalls(ClassMetadata::class, [
            Call::create('setPrimaryTable')->with(['name' => 'pet']),
            Call::create('mapField')->with([
                'id' => true,
                'fieldName' => 'id',
                'type' => 'guid',
            ]),
            Call::create('mapField')->with([
                'fieldName' => 'createdAt',
                'type' => 'datetime',
            ]),
            Call::create('mapField')->with([
                'fieldName' => 'updatedAt',
                'type' => 'datetime',
                'nullable' => true,
            ]),
            Call::create('mapField')->with([
                'fieldName' => 'name',
                'type' => 'string',
            ]),
            Call::create('mapField')->with([
                'fieldName' => 'tag',
                'type' => 'string',
                'nullable' => true,
            ]),
        ]);

        $mapping = new PetMapping();
        $mapping->configureMapping($classMetadata);
    }
}
