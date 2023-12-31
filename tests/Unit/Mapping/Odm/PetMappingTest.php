<?php

declare(strict_types=1);

namespace App\Tests\Unit\Mapping\Odm;

use App\Mapping\Odm\PetMapping;
use App\Model\Vaccination;
use Chubbyphp\Mock\Call;
use Chubbyphp\Mock\MockByCallsTrait;
use Doctrine\ODM\MongoDB\Mapping\ClassMetadata as MongodbODMClassMetadata;
use Doctrine\ODM\MongoDB\Types\Type;
use PHPUnit\Framework\TestCase;

/**
 * @covers \App\Mapping\Odm\PetMapping
 *
 * @internal
 */
final class PetMappingTest extends TestCase
{
    use MockByCallsTrait;

    public function testGetClass(): void
    {
        /** @var MongodbODMClassMetadata $classMetadata */
        $classMetadata = $this->getMockByCalls(MongodbODMClassMetadata::class, [
            Call::create('setCollection')->with('pet'),
            Call::create('addIndex')->with(['name' => 'text'], [])->willReturn([]),
            Call::create('mapField')->with(['name' => 'id', 'id' => true, 'strategy' => 'none'])->willReturn([]),
            Call::create('mapField')->with(['name' => 'createdAt', 'type' => Type::DATE])->willReturn([]),
            Call::create('mapField')->with(['name' => 'updatedAt', 'type' => Type::DATE, 'nullable' => true])->willReturn([]),
            Call::create('mapField')->with(['name' => 'name', 'type' => Type::STRING])->willReturn([]),
            Call::create('mapField')->with(['name' => 'tag', 'type' => Type::STRING, 'nullable' => true])->willReturn([]),
            Call::create('mapManyEmbedded')->with(['name' => 'vaccinations', 'targetDocument' => Vaccination::class, 'storeEmptyArray' => false]),
        ]);

        $mapping = new PetMapping();
        $mapping->configureMapping($classMetadata);
    }
}
