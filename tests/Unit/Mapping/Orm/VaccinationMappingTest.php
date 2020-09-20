<?php

declare(strict_types=1);

namespace App\Tests\Unit\Mapping\Orm;

use App\Mapping\Orm\VaccinationMapping;
use App\Model\Pet;
use Chubbyphp\Mock\Call;
use Chubbyphp\Mock\MockByCallsTrait;
use Doctrine\ORM\Mapping\ClassMetadata;
use PHPUnit\Framework\TestCase;

/**
 * @covers \App\Mapping\Orm\VaccinationMapping
 *
 * @internal
 */
final class VaccinationMappingTest extends TestCase
{
    use MockByCallsTrait;

    public function testGetClass(): void
    {
        /** @var ClassMetadata $classMetadata */
        $classMetadata = $this->getMockByCalls(ClassMetadata::class, [
            Call::create('setPrimaryTable')->with(['name' => 'vaccination']),
            Call::create('mapField')->with([
                'fieldName' => 'id',
                'type' => 'guid',
                'id' => true,
            ]),
            Call::create('mapField')->with([
                'fieldName' => 'name',
                'type' => 'string',
            ]),
            Call::create('mapManyToOne')->with([
                'fieldName' => 'pet',
                'targetEntity' => Pet::class,
                'inversedBy' => 'vaccinations',
                'joinColumns' => [
                    [
                        'name' => 'pet_id',
                        'referencedColumnName' => 'id',
                        'nullable' => false,
                        'unique' => false,
                        'onDelete' => 'CASCADE',
                        'columnDefinition' => null,
                    ],
                ],
            ]),
        ]);

        $mapping = new VaccinationMapping();
        $mapping->configureMapping($classMetadata);
    }
}
