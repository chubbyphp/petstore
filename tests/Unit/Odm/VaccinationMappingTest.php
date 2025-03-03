<?php

declare(strict_types=1);

namespace App\Tests\Unit\Odm;

use App\Odm\VaccinationMapping;
use Chubbyphp\Mock\MockMethod\WithReturn;
use Chubbyphp\Mock\MockObjectBuilder;
use Doctrine\ODM\MongoDB\Mapping\ClassMetadata as MongodbODMClassMetadata;
use Doctrine\ODM\MongoDB\Types\Type;
use PHPUnit\Framework\TestCase;

/**
 * @covers \App\Odm\VaccinationMapping
 *
 * @internal
 */
final class VaccinationMappingTest extends TestCase
{
    public function testGetClass(): void
    {
        $builder = new MockObjectBuilder();

        /** @var MongodbODMClassMetadata $classMetadata */
        $classMetadata = $builder->create(MongodbODMClassMetadata::class, [
            new WithReturn('mapField', [['name' => 'name', 'type' => Type::STRING]], []),
        ]);

        $mapping = new VaccinationMapping();
        $mapping->configureMapping($classMetadata);

        self::assertTrue($classMetadata->isEmbeddedDocument);
    }
}
