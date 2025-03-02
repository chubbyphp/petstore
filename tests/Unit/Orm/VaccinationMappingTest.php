<?php

declare(strict_types=1);

namespace App\Tests\Unit\Orm;

use App\Model\Pet;
use App\Orm\VaccinationMapping;
use Chubbyphp\Mock\MockMethod\WithoutReturn;
use Chubbyphp\Mock\MockObjectBuilder;
use Doctrine\ORM\Mapping\ClassMetadata;
use PHPUnit\Framework\Attributes\DoesNotPerformAssertions;
use PHPUnit\Framework\TestCase;

/**
 * @covers \App\Orm\VaccinationMapping
 *
 * @internal
 */
final class VaccinationMappingTest extends TestCase
{
    #[DoesNotPerformAssertions]
    public function testGetClass(): void
    {
        $builder = new MockObjectBuilder();

        /** @var ClassMetadata $classMetadata */
        $classMetadata = $builder->create(ClassMetadata::class, [
            new WithoutReturn('setPrimaryTable', [['name' => 'vaccination']]),
            new WithoutReturn('mapField', [[
                'fieldName' => 'id',
                'type' => 'guid',
                'id' => true,
            ]]),
            new WithoutReturn('mapField', [[
                'fieldName' => 'name',
                'type' => 'string',
            ]]),
            new WithoutReturn('mapManyToOne', [[
                'fieldName' => 'pet',
                'targetEntity' => Pet::class,
                'inversedBy' => 'vaccinations',
                'joinColumns' => [[
                    'name' => 'pet_id',
                    'referencedColumnName' => 'id',
                    'nullable' => false,
                    'unique' => false,
                    'onDelete' => 'CASCADE',
                    'columnDefinition' => null,
                ]],
            ]]),
        ]);

        $mapping = new VaccinationMapping();
        $mapping->configureMapping($classMetadata);
    }
}
