<?php

declare(strict_types=1);

namespace App\Tests\Unit\Orm;

use App\Model\Vaccination;
use App\Orm\PetMapping;
use Chubbyphp\Mock\MockMethod\WithoutReturn;
use Chubbyphp\Mock\MockObjectBuilder;
use Doctrine\ORM\Mapping\ClassMetadata;
use PHPUnit\Framework\Attributes\DoesNotPerformAssertions;
use PHPUnit\Framework\TestCase;

/**
 * @covers \App\Orm\PetMapping
 *
 * @internal
 */
final class PetMappingTest extends TestCase
{
    #[DoesNotPerformAssertions]
    public function testGetClass(): void
    {
        $builder = new MockObjectBuilder();

        /** @var ClassMetadata $classMetadata */
        $classMetadata = $builder->create(ClassMetadata::class, [
            new WithoutReturn('setPrimaryTable', [['name' => 'pet']]),
            new WithoutReturn('mapField', [[
                'fieldName' => 'id',
                'type' => 'guid',
                'id' => true,
            ]]),
            new WithoutReturn('mapField', [[
                'fieldName' => 'createdAt',
                'type' => 'datetime_immutable',
            ]]),
            new WithoutReturn('mapField', [[
                'nullable' => true,
                'fieldName' => 'updatedAt',
                'type' => 'datetime_immutable',
            ]]),
            new WithoutReturn('mapField', [[
                'fieldName' => 'name',
                'type' => 'string',
            ]]),
            new WithoutReturn('mapField', [[
                'nullable' => true,
                'fieldName' => 'tag',
                'type' => 'string',
            ]]),
            new WithoutReturn('mapOneToMany', [[
                'fieldName' => 'vaccinations',
                'targetEntity' => Vaccination::class,
                'mappedBy' => 'pet',
                'cascade' => ['ALL'],
                'orphanRemoval' => true,
            ]]),
        ]);

        $mapping = new PetMapping();
        $mapping->configureMapping($classMetadata);
    }
}
