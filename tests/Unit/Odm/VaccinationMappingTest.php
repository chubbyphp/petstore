<?php

declare(strict_types=1);

namespace App\Tests\Unit\Odm;

use App\Odm\VaccinationMapping;
use Chubbyphp\Mock\Call;
use Chubbyphp\Mock\MockByCallsTrait;
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
    use MockByCallsTrait;

    public function testGetClass(): void
    {
        /** @var MongodbODMClassMetadata $classMetadata */
        $classMetadata = $this->getMockByCalls(MongodbODMClassMetadata::class, [
            Call::create('mapField')->with(['name' => 'name', 'type' => Type::STRING])->willReturn([]),
        ]);

        $mapping = new VaccinationMapping();
        $mapping->configureMapping($classMetadata);

        self::assertTrue($classMetadata->isEmbeddedDocument);
    }
}
