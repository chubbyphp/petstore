<?php

declare(strict_types=1);

namespace App\Mapping\Orm;

use Chubbyphp\DoctrineDbServiceProvider\Driver\ClassMapMappingInterface;
use Doctrine\ORM\Mapping\ClassMetadata;

final class PetMapping implements ClassMapMappingInterface
{
    /**
     * @param ClassMetadata $metadata
     *
     * @throws MappingException
     */
    public function configureMapping(ClassMetadata $metadata)
    {
        $metadata->setPrimaryTable(['name' => 'pet']);
        $metadata->mapField([
            'id' => true,
            'fieldName' => 'id',
            'type' => 'string',
            'length' => 36,
        ]);
        $metadata->mapField([
            'fieldName' => 'createdAt',
            'type' => 'datetime',
        ]);
        $metadata->mapField([
            'fieldName' => 'updatedAt',
            'type' => 'datetime',
            'nullable' => true,
        ]);
        $metadata->mapField([
            'fieldName' => 'name',
            'type' => 'string',
        ]);
        $metadata->mapField([
            'fieldName' => 'tag',
            'type' => 'string',
            'nullable' => true,
        ]);
    }
}
