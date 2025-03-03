<?php

declare(strict_types=1);

namespace App\Tests\Unit\Odm;

use App\Model\Vaccination;
use App\Odm\PetMapping;
use Chubbyphp\Mock\MockMethod\WithoutReturn;
use Chubbyphp\Mock\MockMethod\WithReturn;
use Chubbyphp\Mock\MockObjectBuilder;
use Doctrine\ODM\MongoDB\Mapping\ClassMetadata as MongodbODMClassMetadata;
use Doctrine\ODM\MongoDB\Types\Type;
use PHPUnit\Framework\Attributes\DoesNotPerformAssertions;
use PHPUnit\Framework\TestCase;

/**
 * @covers \App\Odm\PetMapping
 *
 * @internal
 */
final class PetMappingTest extends TestCase
{
    #[DoesNotPerformAssertions]
    public function testGetClass(): void
    {
        $builder = new MockObjectBuilder();

        /** @var MongodbODMClassMetadata $classMetadata */
        $classMetadata = $builder->create(MongodbODMClassMetadata::class, [
            new WithoutReturn('setCollection', ['pet']),
            new WithoutReturn('addIndex', [['name' => 'text'], []]),
            new WithReturn(
                'mapField',
                [['name' => 'id', 'id' => true, 'strategy' => 'none']],
                [],
            ),
            new WithReturn(
                'mapField',
                [['name' => 'createdAt', 'type' => Type::DATE]],
                [],
            ),
            new WithReturn(
                'mapField',
                [['name' => 'updatedAt', 'type' => Type::DATE, 'nullable' => true]],
                [],
            ),
            new WithReturn(
                'mapField',
                [['name' => 'name', 'type' => Type::STRING]],
                [],
            ),
            new WithReturn(
                'mapField',
                [['name' => 'tag', 'type' => Type::STRING, 'nullable' => true]],
                [],
            ),
            new WithReturn(
                'mapManyEmbedded',
                [['name' => 'vaccinations', 'targetDocument' => Vaccination::class, 'storeEmptyArray' => false]],
                [],
            ),
        ]);

        $mapping = new PetMapping();
        $mapping->configureMapping($classMetadata);
    }
}
