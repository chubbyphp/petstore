<?php

declare(strict_types=1);

namespace App\Mapping\Odm;

use App\Model\Vaccination;
use Chubbyphp\Laminas\Config\Doctrine\Persistence\Mapping\Driver\ClassMapMappingInterface;
use Doctrine\ODM\MongoDB\Mapping\ClassMetadata as MongodbODMClassMetadata;
use Doctrine\ODM\MongoDB\Types\Type;
use Doctrine\Persistence\Mapping\ClassMetadata;

final class PetMapping implements ClassMapMappingInterface
{
    /**
     * @param MongodbODMClassMetadata $metadata
     */
    public function configureMapping(ClassMetadata $metadata): void
    {
        $metadata->setCollection('pet');
        $metadata->addIndex(['name' => 'text']);
        $metadata->mapField(['name' => 'id', 'id' => true, 'strategy' => 'none']);
        $metadata->mapField(['name' => 'createdAt', 'type' => Type::DATE]);
        $metadata->mapField(['name' => 'updatedAt', 'type' => Type::DATE, 'nullable' => true]);
        $metadata->mapField(['name' => 'name', 'type' => Type::STRING]);
        $metadata->mapField(['name' => 'tag', 'type' => Type::STRING, 'nullable' => true]);
        $metadata->mapManyEmbedded(['name' => 'vaccinations', 'targetDocument' => Vaccination::class]);
    }
}
