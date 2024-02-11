<?php

declare(strict_types=1);

namespace App\Odm;

use Chubbyphp\Laminas\Config\Doctrine\Persistence\Mapping\Driver\ClassMapMappingInterface;
use Doctrine\ODM\MongoDB\Mapping\ClassMetadata as MongodbODMClassMetadata;
use Doctrine\ODM\MongoDB\Types\Type;
use Doctrine\Persistence\Mapping\ClassMetadata;

final class VaccinationMapping implements ClassMapMappingInterface
{
    /**
     * @param MongodbODMClassMetadata $metadata
     */
    public function configureMapping(ClassMetadata $metadata): void
    {
        $metadata->isEmbeddedDocument = true;
        $metadata->mapField(['name' => 'name', 'type' => Type::STRING]);
    }
}
