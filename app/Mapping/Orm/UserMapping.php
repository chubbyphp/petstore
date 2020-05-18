<?php

declare(strict_types=1);

namespace App\Mapping\Orm;

use Chubbyphp\DoctrineDbServiceProvider\Driver\ClassMapMappingInterface;
use Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;
use Doctrine\ORM\Mapping\ClassMetadata;

final class UserMapping implements ClassMapMappingInterface
{
    public function configureMapping(ClassMetadata $metadata): void
    {
        $builder = new ClassMetadataBuilder($metadata);
        $builder->setTable('"user"');
        $builder->createField('id', 'guid')->isPrimaryKey()->build();
        $builder->addField('createdAt', 'datetime');
        $builder->addField('updatedAt', 'datetime', ['nullable' => true]);
        $builder->addField('username', 'string');
        $builder->addField('password', 'string');
    }
}
