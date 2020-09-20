<?php

declare(strict_types=1);

namespace App\Tests\Unit\Mapping\Orm;

use App\Mapping\Orm\PetMapping;
use App\Model\Vaccination;
use Chubbyphp\Mock\Call;
use Chubbyphp\Mock\MockByCallsTrait;
use Doctrine\ORM\Mapping\ClassMetadata;
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
        /** @var ClassMetadata $classMetadata */
        $classMetadata = $this->getMockByCalls(ClassMetadata::class, [
            Call::create('setPrimaryTable')->with(['name' => 'pet']),
            Call::create('mapField')->with([
                'fieldName' => 'id',
                'type' => 'guid',
                'id' => true,
            ]),
            Call::create('mapField')->with([
                'fieldName' => 'createdAt',
                'type' => 'datetime',
            ]),
            Call::create('mapField')->with([
                'nullable' => true,
                'fieldName' => 'updatedAt',
                'type' => 'datetime',
            ]),
            Call::create('mapField')->with([
                'fieldName' => 'name',
                'type' => 'string',
            ]),
            Call::create('mapField')->with([
                'nullable' => true,
                'fieldName' => 'tag',
                'type' => 'string',
            ]),
            Call::create('mapOneToMany')->with([
                'fieldName' => 'vaccinations',
                'targetEntity' => Vaccination::class,
                'mappedBy' => 'pet',
                'cascade' => ['ALL'],
                'orphanRemoval' => true,
            ]),
        ]);

        $mapping = new PetMapping();
        $mapping->configureMapping($classMetadata);
    }
}
